<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Praktikum;

use App\Models\Praktikum;
use App\Models\JawabanPraktikum;
use App\Services\Penilaian\NormalisasiJawaban;
use Livewire\Component;
use Livewire\WithFileUploads;

class Kerjakan extends Component
{
    use WithFileUploads;

    public Praktikum $praktikum;
    public $soals = [];

    // State jawaban array: [soal_id => 'jawaban_teks']
    public $jawaban = [];
    
    // State pembukaan hint: [soal_id => hint_index_yg_terbuka]
    public $hintTerbuka = [];
    
    // Alat Bantu State
    public $scratchpad_ip = '192.168.1.0';
    
    // File upload state (bukti_praktik)
    public $file_bukti;
    
    public function mount(Praktikum $praktikum)
    {
        $murid = auth()->user()->murid;
        if ($praktikum->materi->level > $murid->getCurrentPblLevel()) {
            abort(403, 'Anda belum membuka level pembelajaran ini.');
        }

        $this->praktikum = $praktikum;
        $this->soals = $praktikum->soalPraktikums()->aktif()->orderBy('urutan')->get();

        foreach ($this->soals as $soal) {
            $this->jawaban[$soal->id] = '';
            $this->hintTerbuka[$soal->id] = -1;
        }
    }

    public function bukaHint($soal_id, $totalHint)
    {
        if ($this->hintTerbuka[$soal_id] < $totalHint - 1) {
            $this->hintTerbuka[$soal_id]++;
        }
    }

    public function updatedFileBukti()
    {
        $this->validate([
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pkt,pka|max:10240',
        ], [
            'file_bukti.mimes' => 'Format file yang diizinkan hanya JPG, PNG, PKT, atau PKA.',
            'file_bukti.max' => 'Ukuran file maksimal adalah 10MB.'
        ]);
    }

    public function submitPraktikum(NormalisasiJawaban $normalisasi, \App\Actions\UpdateProgressPblAction $action)
    {
        $this->validate([
            'jawaban.*' => 'required|string|max:1000',
            'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pkt,pka|max:10240',
        ], [
            'jawaban.*.required' => 'Seluruh soal wajib diisi.',
            'jawaban.*.max' => 'Jawaban tidak boleh lebih dari 1000 karakter.',
            'file_bukti.required' => 'Anda wajib mengunggah file bukti hasil konfigurasi (Screenshot/File PKT).'
        ]);

        $murid = auth()->user()->murid;

        $pathBukti = $this->file_bukti->store('praktikum', 'private');

        foreach ($this->soals as $soal) {
            $teksJawaban = $this->jawaban[$soal->id];
            
            $status = \App\Enums\StatusPenilaian::MENUNGGU_AI;
            $skor = 0;

            if ($soal->tipe->value === 'isian_singkat') {
                $status = \App\Enums\StatusPenilaian::FINAL;
                if ($normalisasi->cocokkan($teksJawaban, $soal->kunci_jawaban)) {
                    $skor = $soal->skor_maks;
                }
            }

            JawabanPraktikum::create([
                'soal_praktikum_id' => $soal->id,
                'murid_id' => $murid->id,
                'jawaban_teks' => $teksJawaban,
                'file_bukti_path' => $pathBukti,
                'status' => $status,
                'skor_ai' => $skor,
                'skor_final' => $skor,
                'sampel_review' => false,
            ]);
        }

        if ($murid->getPblStateForMateri($this->praktikum->materi->id) === 'praktikum') {
            $action->execute($murid, $this->praktikum->materi, 'praktikum', 100);
        }

        session()->flash('success', 'Praktikum berhasil disubmit! Bukti dan jawaban Anda telah tersimpan dengan aman.');
        return $this->redirectRoute('murid.materi.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.murid.praktikum.kerjakan')
            ->layout('layouts.app', ['title' => 'Kerjakan Praktikum']);
    }
}
