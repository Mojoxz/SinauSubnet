<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Quiz;

use App\Models\HasilQuiz;
use App\Models\JawabanQuiz;
use App\Models\Quiz;
use App\Services\Penilaian\NormalisasiJawaban;
use App\Actions\HitungSkorPoinQuizAction;
use App\Actions\UpdateLeaderboardAction;
use App\Actions\CheckBadgeEligibilityAction;
use App\Actions\UpdateProgressPblAction;
use Livewire\Component;

class Kerjakan extends Component
{
    public Quiz $quiz;
    public HasilQuiz $hasilQuiz;
    public $soals = [];

    // State jawaban array: [soal_id => 'opsi'/'teks']
    public $jawaban = [];
    
    // State Timer dari Server
    public $sisaDetik = 0;

    public function mount(Quiz $quiz)
    {
        $murid = auth()->user()->murid;

        $this->quiz = $quiz;
        
        $hasil = HasilQuiz::where('quiz_id', $quiz->id)->where('murid_id', $murid->id)->first();
        if (!$hasil) {
            return redirect()->route('murid.quiz.mulai', $quiz->id);
        }
        
        if ($hasil->waktu_selesai) {
            return redirect()->route('murid.quiz.hasil', $hasil->id);
        }

        $this->hasilQuiz = $hasil;

        // --- SERVER SIDE TIMER CALCULATION ---
        $waktuMulai = \Carbon\Carbon::parse($hasil->waktu_mulai);
        $waktuHabis = (clone $waktuMulai)->addMinutes($quiz->durasi_menit);
        
        $this->sisaDetik = now()->diffInSeconds($waktuHabis, false); // false = izinkan negatif
        
        if ($this->sisaDetik <= 0) {
            // Waktu sudah habis, auto-submit paksa
            return $this->prosesSubmit(true);
        }

        // --- STATE RECOVERY ---
        // Ambil soal yang aktif
        $this->soals = $quiz->soalQuizzes()->aktif()->orderBy('urutan')->get();

        // Ambil jawaban tersimpan di database jika siswa merefresh halaman
        $jawabanTersimpan = JawabanQuiz::whereIn('soal_quiz_id', $this->soals->pluck('id'))
            ->where('murid_id', $murid->id)
            ->get()
            ->keyBy('soal_quiz_id');

        foreach ($this->soals as $soal) {
            // Restore state kalau ada, kalau tidak ya kosongan
            $this->jawaban[$soal->id] = $jawabanTersimpan->has($soal->id) 
                ? $jawabanTersimpan[$soal->id]->jawaban 
                : '';
        }
    }

    // Dipanggil setiap kali murid mengetik/mengubah jawaban agar tersimpan ke database secara real-time
    // (Mencegah kehilangan data jika tiba-tiba tab tertutup)
    public function updatedJawaban($value, $key)
    {
        JawabanQuiz::updateOrCreate(
            ['soal_quiz_id' => $key, 'hasil_quiz_id' => $this->hasilQuiz->id],
            ['jawaban' => $value, 'status_penilaian' => \App\Enums\StatusPenilaian::MENUNGGU_AI->value, 'skor_ai' => 0, 'skor_final' => 0]
        );
    }

    public function submitQuiz(
        NormalisasiJawaban $normalisasi, 
        HitungSkorPoinQuizAction $hitungSkorAction, 
        UpdateLeaderboardAction $leaderboardAction,
        CheckBadgeEligibilityAction $badgeAction,
        UpdateProgressPblAction $pblAction
    ) {
        $this->prosesSubmit(false, $normalisasi, $hitungSkorAction, $leaderboardAction, $badgeAction, $pblAction);
    }

    // Method privat untuk mengeksekusi submission, dipanggil manual atau auto (waktu habis)
    private function prosesSubmit(
        $isAutoTimeout = false,
        ?NormalisasiJawaban $normalisasi = null, 
        ?HitungSkorPoinQuizAction $hitungSkorAction = null, 
        ?UpdateLeaderboardAction $leaderboardAction = null,
        ?CheckBadgeEligibilityAction $badgeAction = null,
        ?UpdateProgressPblAction $pblAction = null
    ) {
        // Jika parameter injeksi belum diberikan karena dipanggil dari mount (Timeout), resolve lewat app()
        $normalisasi = $normalisasi ?? app(NormalisasiJawaban::class);
        $hitungSkorAction = $hitungSkorAction ?? app(HitungSkorPoinQuizAction::class);
        $leaderboardAction = $leaderboardAction ?? app(UpdateLeaderboardAction::class);
        $badgeAction = $badgeAction ?? app(CheckBadgeEligibilityAction::class);
        $pblAction = $pblAction ?? app(UpdateProgressPblAction::class);

        $murid = auth()->user()->murid;

        // Tandai kuis sebagai selesai (kunci waktu)
        $this->hasilQuiz->waktu_selesai = now();
        $this->hasilQuiz->save();

        // 1. Lakukan Evaluasi Otomatis (Auto-grading)
        foreach ($this->soals as $soal) {
            $teksJawaban = $this->jawaban[$soal->id] ?? '';
            
            $status = \App\Enums\StatusPenilaian::MENUNGGU_AI;
            $skor = 0;

            if ($soal->tipe->value === 'pilihan_ganda') {
                $status = \App\Enums\StatusPenilaian::FINAL;
                // Kunci jawaban PG dalam array, contoh ['Switch'], kita cocokan case-insensitive
                if (in_array($teksJawaban, $soal->kunci_jawaban ?? [])) {
                    $skor = $soal->skor_maks;
                }
            } elseif ($soal->tipe->value === 'isian_singkat') {
                $status = \App\Enums\StatusPenilaian::FINAL;
                if ($normalisasi->cocokkan($teksJawaban, $soal->kunci_jawaban)) {
                    $skor = $soal->skor_maks;
                }
            }

            JawabanQuiz::updateOrCreate(
                ['soal_quiz_id' => $soal->id, 'hasil_quiz_id' => $this->hasilQuiz->id],
                [
                    'jawaban' => $teksJawaban,
                    'status_penilaian' => $status->value,
                    'skor_ai' => $skor,
                    'skor_final' => $skor,
                    'sampel_review' => false,
                ]
            );
        }

        // 2. Hitung Total Skor, Penyesuaian Skor AI & Speed Bonus Gamifikasi
        $hasil = $hitungSkorAction->execute($this->hasilQuiz);
        
        // 3. Update Papan Peringkat (Ledger)
        if ($hasil->total_poin_diperoleh > 0) {
            $leaderboardAction->execute($murid, $hasil->total_poin_diperoleh, 'quiz', $hasil->id);
            // 4. Periksa Lencana (Badge)
            $badgeAction->execute($murid);
        }

        // 5. Update Progress PBL murid ke tahap Materi Berikutnya (Jika lulus KKM)
        // KKM menggunakan persentase. Misal KKM 75, total persentase skor = (skor / max) * 100
        $persentase = ($hasil->skor_total / max($hasil->skor_maksimal, 1)) * 100;
        
        if ($persentase >= $this->quiz->kkm && $murid->getPblStateForMateri($this->quiz->materi->id) === 'quiz') {
            // Lulus quiz! Tandai quiz selesai (persen_quiz = 100), sistem pbl dinamis akan otomatis membuka materi berikutnya
            $pblAction->execute($murid, $this->quiz->materi, 'quiz', 100);
            session()->flash('success_gamification', 'Selamat! Anda lulus dan berhak melanjutkan ke Level selanjutnya.');
        } elseif ($persentase < $this->quiz->kkm) {
            session()->flash('warning_gamification', 'Nilai Anda masih di bawah KKM. Guru akan mengevaluasi jawaban uraian Anda.');
        }

        if ($isAutoTimeout) {
            session()->flash('warning', 'Waktu ujian telah habis. Kuis disubmit secara otomatis.');
        } else {
            session()->flash('success', 'Kuis berhasil diselesaikan.');
        }

        return redirect()->route('murid.quiz.hasil', $this->hasilQuiz->id);
    }

    public function render()
    {
        return view('livewire.murid.quiz.kerjakan')
            ->layout('layouts.app', ['title' => 'Mengerjakan Quiz']);
    }
}
