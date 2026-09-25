<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\HasilQuiz;
use App\Models\JawabanQuiz;
use App\Models\SoalQuiz;
use App\Services\Penilaian\NormalisasiJawaban;
use App\Actions\HitungSkorPoinQuizAction;
use App\Actions\UpdateLeaderboardAction;
use App\Actions\CheckBadgeEligibilityAction;
use App\Actions\UpdateProgressPblAction;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinalisasiQuizKedaluwarsa extends Command
{
    protected $signature = 'sinausubnet:finalisasi-quiz-kedaluwarsa';
    protected $description = 'Menutup dan memfinalisasi otomatis percobaan kuis murid yang telah kedaluwarsa (lewat batas waktu).';

    public function handle(
        NormalisasiJawaban $normalisasi, 
        HitungSkorPoinQuizAction $hitungSkorAction, 
        UpdateLeaderboardAction $leaderboardAction,
        CheckBadgeEligibilityAction $badgeAction,
        UpdateProgressPblAction $pblAction
    ) {
        $this->info('Memulai pengecekan kuis kedaluwarsa...');

        // Cari semua hasil_quiz yang belum disubmit (waktu_selesai null)
        $hasilQuizzes = HasilQuiz::whereNull('waktu_selesai')
            ->with(['quiz.materi', 'murid.user'])
            ->get();

        $jumlahDifinalisasi = 0;

        foreach ($hasilQuizzes as $hasil) {
            $waktuMulai = Carbon::parse($hasil->waktu_mulai);
            $waktuHabis = (clone $waktuMulai)->addMinutes($hasil->quiz->durasi_menit);

            // Jika waktu saat ini sudah melewati waktuHabis + toleransi 1 menit
            if (now()->greaterThanOrEqualTo($waktuHabis->addMinutes(1))) {
                $this->info("Memfinalisasi Kuis ID {$hasil->quiz_id} untuk Murid ID {$hasil->murid_id}...");
                
                // Kunci waktu selesai = batas waktu maksimal
                $hasil->waktu_selesai = $waktuHabis->subMinute(); // Kembalikan tanpa toleransi
                $hasil->save();

                $soals = $hasil->quiz->soalQuizzes()->aktif()->get();

                // Dapatkan jawaban yang sudah disimpan oleh fitur auto-save
                $jawabanTersimpan = JawabanQuiz::where('hasil_quiz_id', $hasil->id)
                    ->get()
                    ->keyBy('soal_quiz_id');

                foreach ($soals as $soal) {
                    $teksJawaban = $jawabanTersimpan->has($soal->id) ? $jawabanTersimpan[$soal->id]->jawaban : '';
                    
                    $status = \App\Enums\StatusPenilaian::MENUNGGU_AI;
                    $skor = 0;

                    if ($soal->tipe->value === 'pilihan_ganda') {
                        $status = \App\Enums\StatusPenilaian::FINAL;
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
                        ['soal_quiz_id' => $soal->id, 'hasil_quiz_id' => $hasil->id],
                        [
                            'jawaban' => $teksJawaban,
                            'status_penilaian' => $status->value,
                            'skor_ai' => $skor,
                            'skor_final' => $skor,
                            'sampel_review' => false,
                        ]
                    );
                }

                // Kalkulasi skor akhir & Poin
                $hasilTerkalkulasi = $hitungSkorAction->execute($hasil);

                if ($hasilTerkalkulasi->total_poin_diperoleh > 0) {
                    $leaderboardAction->execute($hasil->murid, $hasilTerkalkulasi->total_poin_diperoleh, 'quiz', $hasil->id);
                    $badgeAction->execute($hasil->murid);
                }

                // Cek kelulusan PBL
                $persentase = ($hasilTerkalkulasi->skor_total / max($hasilTerkalkulasi->skor_maksimal, 1)) * 100;
                if ($persentase >= $hasil->quiz->kkm && $hasil->murid->pbl_level === $hasil->quiz->materi->level && $hasil->murid->pbl_status === 'quiz') {
                    $pblAction->execute($hasil->murid, $hasil->quiz->materi->level + 1, 'materi');
                }

                $jumlahDifinalisasi++;
                Log::info("Kuis ID {$hasil->quiz_id} milik Murid {$hasil->murid_id} ditutup otomatis.");
            }
        }

        $this->info("Selesai! {$jumlahDifinalisasi} sesi kuis telah ditutup paksa dan dievaluasi.");
    }
}
