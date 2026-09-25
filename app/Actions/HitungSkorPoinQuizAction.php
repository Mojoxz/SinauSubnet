<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\HasilQuiz;
use App\Models\SoalQuiz;
use App\Services\Penilaian\NormalisasiJawaban;

class HitungSkorPoinQuizAction
{
    public function __construct(
        private NormalisasiJawaban $normalisasiService
    ) {}

    /**
     * Mengeksekusi perhitungan skor kognitif sementara dan total poin gamifikasi.
     * Dipanggil saat Kuis disubmit oleh murid.
     */
    public function execute(HasilQuiz $hasilQuiz): void
    {
        $jawabanList = $hasilQuiz->jawabanQuizzes()->with('soalQuiz')->get();
        $quiz = $hasilQuiz->quiz;

        $skorTotal = 0;
        $poinTotal = 0;
        $jumlahBenarPgAtauIsian = 0;
        $totalSoalPgAtauIsian = 0;

        foreach ($jawabanList as $jawaban) {
            $soal = $jawaban->soalQuiz;
            
            // Evaluasi PG & Isian Singkat secara langsung (instan)
            if (in_array($soal->tipe->value, ['pilihan_ganda', 'isian'])) {
                $totalSoalPgAtauIsian++;
                
                $isBenar = false;
                
                if ($soal->tipe->value === 'pilihan_ganda') {
                    // Cek exact match untuk opsi A, B, C, D, E (biasanya di array index 0)
                    $isBenar = isset($soal->kunci_jawaban[0]) && $jawaban->jawaban === $soal->kunci_jawaban[0];
                } else {
                    // Isian Singkat (menggunakan NormalisasiService karena input user bisa beragam)
                    $isBenar = $this->normalisasiService->cekBenar($jawaban->jawaban, $soal->kunci_jawaban ?? []);
                }

                if ($isBenar) {
                    $skorTotal += $soal->skor_maks;
                    $poinTotal += $soal->poin_dasar;
                    $jumlahBenarPgAtauIsian++;
                    
                    // Set status dan skor final langsung untuk jawaban ini
                    $jawaban->update([
                        'skor_final' => $soal->skor_maks,
                        'status_penilaian' => \App\Enums\StatusPenilaian::FINAL_OTOMATIS,
                    ]);
                } else {
                    // Salah
                    $jawaban->update([
                        'skor_final' => 0,
                        'status_penilaian' => \App\Enums\StatusPenilaian::FINAL_OTOMATIS,
                    ]);
                }
            } 
            // Soal Uraian diproses asinkron oleh AI, jadi tidak dihitung skornya di sini,
            // Namun, karena ini submit awal, tetap pastikan tercatat dengan status menunggu_ai.
            else {
                 $jawaban->update([
                     'status_penilaian' => \App\Enums\StatusPenilaian::MENUNGGU_AI,
                 ]);
            }
        }

        // Hitung Bonus Kecepatan
        $bonus = 0;
        if ($totalSoalPgAtauIsian > 0 && $hasilQuiz->waktu_selesai) {
            $durasiDetik = $quiz->durasi_menit * 60;
            $waktuSelesaiTs = $hasilQuiz->waktu_selesai->timestamp;
            $waktuMulaiTs = $hasilQuiz->waktu_mulai->timestamp;
            
            $waktuPengerjaan = abs($waktuSelesaiTs - $waktuMulaiTs);
            $sisaWaktuDetik = max(0, $durasiDetik - $waktuPengerjaan);
            
            // Rumus bonus (lihat PRD Bab 7.6)
            $rasioWaktu = $sisaWaktuDetik / $durasiDetik;
            $rasioBenar = $jumlahBenarPgAtauIsian / $totalSoalPgAtauIsian;
            
            $bonus = (int) round($quiz->bonus_kecepatan_maks * $rasioWaktu * $rasioBenar);
        }

        $poinTotal += $bonus;

        // Simpan agregasi ke HasilQuiz
        $hasilQuiz->update([
            'skor_total' => $skorTotal, // Skor sementara (baru PG/Isian yang dinilai)
            'poin_total' => $poinTotal, // Poin final gamifikasi
        ]);
    }
}
