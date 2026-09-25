<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Praktikum;

use App\Models\Praktikum;
use App\Models\SoalPraktikum;
use Livewire\Component;
use App\Enums\LevelBloom;
use App\Enums\TipeSoal;

class Detail extends Component
{
    public Praktikum $praktikum;
    public $soalList = [];

    // State untuk form modal / inline tambah soal
    public $modeForm = false;
    public $soal_id = null;
    
    public $pertanyaan = '';
    public $tipe = TipeSoal::PILIHAN_GANDA->value;
    public $level_bloom = LevelBloom::C3->value;
    public $skor_maks = 10;
    public $pembahasan = '';
    public $urutan = 1;
    
    // Arrays statis
    public $kunci_jawaban = [];
    public $rubrik = [];
    public $hint = [];

    // State sementara untuk UI input array
    public $tempKunci = '';
    public $tempHint = '';
    public $tempRubrikAspek = '';
    public $tempRubrikSkor = 1;

    protected function rules()
    {
        return [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|string',
            'level_bloom' => 'required|string',
            'skor_maks' => 'required|integer|min:1',
            'urutan' => 'required|integer|min:1',
            'pembahasan' => 'nullable|string',
            'kunci_jawaban' => 'nullable|array',
            'rubrik' => 'nullable|array',
            'hint' => 'nullable|array',
        ];
    }

    public function mount(Praktikum $praktikum)
    {
        $this->authorize('view', $praktikum);
        $this->praktikum = $praktikum;
        $this->loadSoal();
    }

    public function loadSoal()
    {
        $this->soalList = $this->praktikum->soalPraktikums()->orderBy('urutan')->get();
    }

    public function toggleForm()
    {
        $this->resetForm();
        $this->urutan = $this->soalList->count() + 1;
        $this->modeForm = true;
    }

    public function editSoal($id)
    {
        $soal = SoalPraktikum::findOrFail($id);
        $this->soal_id = $soal->id;
        $this->pertanyaan = $soal->pertanyaan;
        $this->tipe = $soal->tipe->value;
        $this->level_bloom = $soal->level_bloom->value;
        $this->skor_maks = $soal->skor_maks;
        $this->pembahasan = $soal->pembahasan;
        $this->urutan = $soal->urutan;
        $this->kunci_jawaban = $soal->kunci_jawaban ?? [];
        $this->rubrik = $soal->rubrik ?? [];
        $this->hint = $soal->hint ?? [];
        
        $this->modeForm = true;
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->soal_id = null;
        $this->pertanyaan = '';
        $this->tipe = TipeSoal::ISIAN_SINGKAT->value; // Praktikum biasanya isian/uraian
        $this->level_bloom = LevelBloom::C4->value;
        $this->skor_maks = 10;
        $this->pembahasan = '';
        $this->kunci_jawaban = [];
        $this->rubrik = [];
        $this->hint = [];
        $this->modeForm = false;
    }

    // --- Helper Array Inputs ---
    public function addKunci()
    {
        if (trim($this->tempKunci) !== '') {
            $this->kunci_jawaban[] = trim($this->tempKunci);
            $this->tempKunci = '';
        }
    }
    public function removeKunci($index) { unset($this->kunci_jawaban[$index]); $this->kunci_jawaban = array_values($this->kunci_jawaban); }

    public function addHint()
    {
        if (trim($this->tempHint) !== '') {
            $this->hint[] = trim($this->tempHint);
            $this->tempHint = '';
        }
    }
    public function removeHint($index) { unset($this->hint[$index]); $this->hint = array_values($this->hint); }

    public function addRubrik()
    {
        if (trim($this->tempRubrikAspek) !== '' && $this->tempRubrikSkor > 0) {
            $this->rubrik[] = [
                'aspek' => trim($this->tempRubrikAspek),
                'skor' => (int) $this->tempRubrikSkor
            ];
            $this->tempRubrikAspek = '';
            $this->tempRubrikSkor = 1;
        }
    }
    public function removeRubrik($index) { unset($this->rubrik[$index]); $this->rubrik = array_values($this->rubrik); }


    public function simpanSoal()
    {
        $this->validate();

        // Validasi khusus Rubrik: Total Skor kriteria == Skor Maks soal
        if ($this->tipe === TipeSoal::URAIAN->value && count($this->rubrik) > 0) {
            $totalRubrik = array_sum(array_column($this->rubrik, 'skor'));
            if ($totalRubrik !== (int)$this->skor_maks) {
                $this->addError('skor_maks', "Total skor di rubrik ($totalRubrik) tidak sama dengan Skor Maksimal soal ($this->skor_maks). Keduanya harus sama persis.");
                return;
            }
        }

        $data = [
            'praktikum_id' => $this->praktikum->id,
            'pertanyaan' => $this->pertanyaan,
            'tipe' => TipeSoal::from($this->tipe),
            'level_bloom' => LevelBloom::from($this->level_bloom),
            'skor_maks' => $this->skor_maks,
            'pembahasan' => $this->pembahasan,
            'urutan' => $this->urutan,
            'kunci_jawaban' => $this->tipe === TipeSoal::URAIAN->value ? null : $this->kunci_jawaban,
            'rubrik' => $this->tipe === TipeSoal::URAIAN->value ? $this->rubrik : null,
            'hint' => $this->hint,
        ];

        if ($this->soal_id) {
            SoalPraktikum::findOrFail($this->soal_id)->update($data);
            session()->flash('success', 'Soal berhasil diperbarui.');
        } else {
            SoalPraktikum::create($data);
            session()->flash('success', 'Soal baru berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->loadSoal();
    }

    public function hapusSoal($id)
    {
        $soal = SoalPraktikum::findOrFail($id);
        
        // Logika Soft-Archive jika soal sudah dijawab
        $sudahDijawab = \App\Models\JawabanPraktikum::where('soal_praktikum_id', $soal->id)->exists();
        
        if ($sudahDijawab) {
            $soal->update(['is_aktif' => false]);
            session()->flash('warning', 'Soal diarsipkan (Soft-Archive) karena sudah ada jawaban dari murid.');
        } else {
            $soal->delete();
            session()->flash('success', 'Soal berhasil dihapus permanen.');
        }
        
        $this->loadSoal();
    }

    public function render()
    {
        return view('livewire.guru.praktikum.detail')
            ->layout('layouts.app', ['title' => 'Kelola Soal Praktikum']);
    }
}
