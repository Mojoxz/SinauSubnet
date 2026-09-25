<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Penilaian;

use App\Models\JawabanQuiz;
use App\Models\JawabanPraktikum;
use App\Models\HasilQuiz;
use Livewire\Component;
use Livewire\WithPagination;

class DaftarUraian extends Component
{
    use WithPagination;

    public $filterTipe = 'semua'; // semua, quiz, praktikum
    public $filterStatus = 'butuh_perhatian'; // butuh_perhatian, divalidasi_guru, semua

    // Modal state untuk Review/Koreksi
    public $modalTerbuka = false;
    public $jawabanAktif = null;
    public $tipeAktif = ''; // 'quiz' / 'praktikum'
    
    // Form Edit
    public $skorFinal = 0;
    public $feedbackFinal = '';

    public function render()
    {
        $thresholdRendah = config('sinausubnet.ai_review.skor_rendah_threshold', 0.30);
        $thresholdTinggi = config('sinausubnet.ai_review.skor_tinggi_threshold', 0.95);

        // Subquery Logika Butuh Perhatian (Ekstrem, Manual, Sampel Review)
        // Kita terapkan pada JawabanQuiz dan JawabanPraktikum
        
        $kueriQuiz = JawabanQuiz::query()
            ->with(['soalQuiz.quiz', 'hasilQuiz.murid.user'])
            ->whereHas('soalQuiz', function($q) {
                $q->where('tipe', 'uraian');
            });

        $kueriPraktikum = JawabanPraktikum::query()
            ->with(['soalPraktikum.praktikum', 'murid.user'])
            ->whereHas('soalPraktikum', function($q) {
                $q->where('tipe', 'uraian');
            });

        // Filter berdasarkan status Review
        if ($this->filterStatus === 'butuh_perhatian') {
            $condition = function($query) use ($thresholdRendah, $thresholdTinggi) {
                $query->whereIn('status_penilaian', ['perlu_manual', 'dinilai_ai'])
                      ->where(function($q) use ($thresholdRendah, $thresholdTinggi) {
                          $q->where('status_penilaian', 'perlu_manual')
                            ->orWhere('sampel_review', true)
                            ->orWhereRaw("skor_ai <= (skor_maks * ?)", [$thresholdRendah])
                            ->orWhereRaw("skor_ai >= (skor_maks * ?)", [$thresholdTinggi]);
                      });
            };
            
            // Join dengan soal untuk dapat skor_maks di query
            $kueriQuiz->join('soal_quiz', 'jawaban_quiz.soal_quiz_id', '=', 'soal_quiz.id')
                      ->select('jawaban_quiz.*', 'soal_quiz.skor_maks')
                      ->where($condition);
                      
            $kueriPraktikum->join('soal_praktikum', 'jawaban_praktikum.soal_praktikum_id', '=', 'soal_praktikum.id')
                           ->select('jawaban_praktikum.*', 'soal_praktikum.skor_maks')
                           ->where($condition);
        } elseif ($this->filterStatus === 'divalidasi_guru') {
            $kueriQuiz->whereIn('status_penilaian', ['divalidasi_guru', 'dikoreksi_guru']);
            $kueriPraktikum->whereIn('status_penilaian', ['divalidasi_guru', 'dikoreksi_guru']);
        }

        // Ambil data (karena tidak bisa UNION dengan mudah jika kolom berbeda/relasi beda, kita ambil terpisah lalu merge manual untuk list kecil)
        $koleksi = collect();

        if ($this->filterTipe === 'semua' || $this->filterTipe === 'quiz') {
            $koleksi = $koleksi->concat($kueriQuiz->latest('updated_at')->get()->map(function($item) {
                $item->jenis = 'quiz';
                return $item;
            }));
        }

        if ($this->filterTipe === 'semua' || $this->filterTipe === 'praktikum') {
            $koleksi = $koleksi->concat($kueriPraktikum->latest('updated_at')->get()->map(function($item) {
                $item->jenis = 'praktikum';
                return $item;
            }));
        }

        // Sort descending
        $daftarJawaban = $koleksi->sortByDesc('updated_at')->values();

        return view('livewire.guru.penilaian.daftar-uraian', [
            'daftarJawaban' => $daftarJawaban
        ])->layout('layouts.app', ['title' => 'Review Penilaian AI (Uraian)']);
    }

    public function bukaReview($id, $jenis)
    {
        $this->tipeAktif = $jenis;
        
        if ($jenis === 'quiz') {
            $this->jawabanAktif = JawabanQuiz::with(['soalQuiz'])->find($id);
        } else {
            $this->jawabanAktif = JawabanPraktikum::with(['soalPraktikum'])->find($id);
        }

        if (!$this->jawabanAktif) return;

        $this->skorFinal = $this->jawabanAktif->skor_final ?? $this->jawabanAktif->skor_ai ?? 0;
        $this->feedbackFinal = $this->jawabanAktif->feedback_final ?? $this->jawabanAktif->feedback_ai ?? '';
        
        $this->modalTerbuka = true;
    }

    public function tutupModal()
    {
        $this->modalTerbuka = false;
        $this->jawabanAktif = null;
    }

    public function simpanValidasi($override = false)
    {
        if (!$this->jawabanAktif) return;

        $soal = $this->tipeAktif === 'quiz' ? $this->jawabanAktif->soalQuiz : $this->jawabanAktif->soalPraktikum;

        $this->validate([
            'skorFinal' => "required|integer|min:0|max:{$soal->skor_maks}",
            'feedbackFinal' => 'nullable|string'
        ]);

        $this->jawabanAktif->skor_final = $this->skorFinal;
        $this->jawabanAktif->feedback_final = $this->feedbackFinal;
        
        if ($override) {
            $this->jawabanAktif->status_penilaian = \App\Enums\StatusPenilaian::DIKOREKSI_GURU->value;
        } else {
            $this->jawabanAktif->status_penilaian = \App\Enums\StatusPenilaian::DIVALIDASI_GURU->value;
        }

        $this->jawabanAktif->ditinjau_oleh = auth()->user()->guru->id;
        $this->jawabanAktif->ditinjau_pada = now();
        $this->jawabanAktif->save();

        // Jika ini dari kuis, kita perlu mengkalkulasi ulang total_skor dan total_poin di hasil_quiz
        // Namun, jika kita panggil HitungSkorPoinQuizAction, poin murid bisa berubah.
        if ($this->tipeAktif === 'quiz') {
            $hasilQuiz = HasilQuiz::find($this->jawabanAktif->hasil_quiz_id);
            if ($hasilQuiz) {
                app(\App\Actions\HitungSkorPoinQuizAction::class)->execute($hasilQuiz);
            }
        }

        session()->flash('success', 'Penilaian berhasil disimpan!');
        $this->tutupModal();
    }
}
