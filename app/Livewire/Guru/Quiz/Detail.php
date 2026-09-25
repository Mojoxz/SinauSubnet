<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Quiz;

use App\Models\Quiz;
use App\Models\SoalQuiz;
use Livewire\Component;
use App\Enums\LevelBloom;
use App\Enums\TipeSoal;

class Detail extends Component
{
    public Quiz $quiz;
    public $soalList = [];

    // State form tambah/edit
    public $modeForm = false;
    public $soal_id = null;
    
    public $pertanyaan = '';
    public $tipe = TipeSoal::PILIHAN_GANDA->value;
    public $level_bloom = LevelBloom::C3->value;
    public $poin_dasar = 10;
    public $skor_maks = 10;
    public $pembahasan = '';
    public $urutan = 1;
    
    // Arrays statis
    public $kunci_jawaban = [];
    public $opsi = []; // Untuk Pilihan Ganda
    public $rubrik = [];

    // State sementara UI input array
    public $tempKunci = '';
    public $tempOpsi = '';
    public $tempRubrikAspek = '';
    public $tempRubrikSkor = 1;

    protected function rules()
    {
        return [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|string',
            'level_bloom' => 'required|string',
            'poin_dasar' => 'required|integer|min:0|max:1000',
            'skor_maks' => 'required|integer|min:1',
            'urutan' => 'required|integer|min:1',
            'pembahasan' => 'nullable|string',
            'kunci_jawaban' => 'nullable|array',
            'opsi' => 'nullable|array',
            'rubrik' => 'nullable|array',
        ];
    }

    public function mount(Quiz $quiz)
    {
        $this->authorize('view', $quiz);
        $this->quiz = $quiz;
        $this->loadSoal();
    }

    public function loadSoal()
    {
        $this->soalList = $this->quiz->soalQuizzes()->orderBy('urutan')->get();
    }

    public function toggleForm()
    {
        $this->resetForm();
        $this->urutan = $this->soalList->count() + 1;
        $this->modeForm = true;
    }

    public function editSoal($id)
    {
        $soal = SoalQuiz::findOrFail($id);
        $this->soal_id = $soal->id;
        $this->pertanyaan = $soal->pertanyaan;
        $this->tipe = $soal->tipe->value;
        $this->level_bloom = $soal->level_bloom->value;
        $this->poin_dasar = $soal->poin_dasar;
        $this->skor_maks = $soal->skor_maks;
        $this->pembahasan = $soal->pembahasan;
        $this->urutan = $soal->urutan;
        
        $this->kunci_jawaban = $soal->kunci_jawaban ?? [];
        $this->opsi = $soal->opsi ?? [];
        $this->rubrik = $soal->rubrik ?? [];
        
        $this->modeForm = true;
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->soal_id = null;
        $this->pertanyaan = '';
        $this->tipe = TipeSoal::PILIHAN_GANDA->value;
        $this->level_bloom = LevelBloom::C3->value;
        $this->poin_dasar = 10;
        $this->skor_maks = 10;
        $this->pembahasan = '';
        $this->kunci_jawaban = [];
        $this->opsi = [];
        $this->rubrik = [];
        $this->modeForm = false;
    }

    // --- Helper Arrays ---
    public function addOpsi()
    {
        if (trim($this->tempOpsi) !== '') {
            $this->opsi[] = trim($this->tempOpsi);
            $this->tempOpsi = '';
        }
    }
    public function removeOpsi($index) { unset($this->opsi[$index]); $this->opsi = array_values($this->opsi); }

    public function addKunci()
    {
        if (trim($this->tempKunci) !== '') {
            $this->kunci_jawaban[] = trim($this->tempKunci);
            $this->tempKunci = '';
        }
    }
    public function removeKunci($index) { unset($this->kunci_jawaban[$index]); $this->kunci_jawaban = array_values($this->kunci_jawaban); }

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

        // Validasi Opsi jika PG
        if ($this->tipe === TipeSoal::PILIHAN_GANDA->value && empty($this->opsi)) {
            $this->addError('opsi', 'Soal Pilihan Ganda harus memiliki opsi jawaban.');
            return;
        }

        // Validasi khusus Rubrik: Total Skor kriteria == Skor Maks soal
        if ($this->tipe === TipeSoal::URAIAN->value && count($this->rubrik) > 0) {
            $totalRubrik = array_sum(array_column($this->rubrik, 'skor'));
            if ($totalRubrik !== (int)$this->skor_maks) {
                $this->addError('skor_maks', "Total skor di rubrik ($totalRubrik) tidak sama dengan Skor Maksimal soal ($this->skor_maks).");
                return;
            }
        }

        $data = [
            'quiz_id' => $this->quiz->id,
            'pertanyaan' => $this->pertanyaan,
            'tipe' => TipeSoal::from($this->tipe),
            'level_bloom' => LevelBloom::from($this->level_bloom),
            'poin_dasar' => $this->poin_dasar,
            'skor_maks' => $this->skor_maks,
            'pembahasan' => $this->pembahasan,
            'urutan' => $this->urutan,
            'kunci_jawaban' => $this->tipe === TipeSoal::URAIAN->value ? null : $this->kunci_jawaban,
            'opsi' => $this->tipe === TipeSoal::PILIHAN_GANDA->value ? $this->opsi : null,
            'rubrik' => $this->tipe === TipeSoal::URAIAN->value ? $this->rubrik : null,
        ];

        if ($this->soal_id) {
            SoalQuiz::findOrFail($this->soal_id)->update($data);
            session()->flash('success', 'Soal Quiz berhasil diperbarui.');
        } else {
            SoalQuiz::create($data);
            session()->flash('success', 'Soal Quiz baru berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->loadSoal();
    }

    public function hapusSoal($id)
    {
        $soal = SoalQuiz::findOrFail($id);
        
        $sudahDijawab = \App\Models\JawabanQuiz::where('soal_quiz_id', $soal->id)->exists();
        
        if ($sudahDijawab) {
            $soal->update(['is_aktif' => false]);
            session()->flash('warning', 'Soal diarsipkan (Soft-Archive) karena murid telah mengerjakannya.');
        } else {
            $soal->delete();
            session()->flash('success', 'Soal berhasil dihapus permanen.');
        }
        
        $this->loadSoal();
    }

    public function render()
    {
        return view('livewire.guru.quiz.detail')
            ->layout('layouts.app', ['title' => 'Kelola Soal Quiz']);
    }
}
