<?php

declare(strict_types=1);

namespace App\Services\Penilaian;

use App\Models\JawabanPraktikum;
use App\Models\JawabanQuiz;

class NilaiCalculator
{
    /**
     * Menghitung nilai berdasarkan level Bloom (C3, C4, C5) untuk seorang murid
     * Menggabungkan data dari JawabanPraktikum dan JawabanQuiz.
     * Mengembalikan persentase (0-100).
     */
    public function hitungNilaiBloom(int $muridId, string $levelBloom): float
    {
        // 1. Ambil Skor & Maks dari Praktikum
        $praktikumData = JawabanPraktikum::where('murid_id', $muridId)
            ->whereHas('soalPraktikum', function ($query) use ($levelBloom) {
                $query->where('level_bloom', $levelBloom);
            })
            ->join('soal_praktikum', 'jawaban_praktikum.soal_praktikum_id', '=', 'soal_praktikum.id')
            ->selectRaw('SUM(jawaban_praktikum.skor_final) as total_skor, SUM(soal_praktikum.skor_maks) as total_maks')
            ->first();

        // 2. Ambil Skor & Maks dari Quiz
        $quizData = JawabanQuiz::whereHas('hasilQuiz', function ($q) use ($muridId) {
                $q->where('murid_id', $muridId);
            })
            ->whereHas('soalQuiz', function ($query) use ($levelBloom) {
                $query->where('level_bloom', $levelBloom);
            })
            ->join('soal_quiz', 'jawaban_quiz.soal_quiz_id', '=', 'soal_quiz.id')
            ->selectRaw('SUM(jawaban_quiz.skor_final) as total_skor, SUM(soal_quiz.skor_maks) as total_maks')
            ->first();

        $totalSkor = ($praktikumData->total_skor ?? 0) + ($quizData->total_skor ?? 0);
        $totalMaks = ($praktikumData->total_maks ?? 0) + ($quizData->total_maks ?? 0);

        if ($totalMaks == 0) {
            return 0.0;
        }

        return round(($totalSkor / $totalMaks) * 100, 2);
    }
    
    /**
     * Hitung total skor kognitif rata-rata dari semua kuis dan praktikum
     */
    public function hitungRataRataKognitif(int $muridId): float
    {
        $c3 = $this->hitungNilaiBloom($muridId, 'C3');
        $c4 = $this->hitungNilaiBloom($muridId, 'C4');
        $c5 = $this->hitungNilaiBloom($muridId, 'C5');
        
        $bobot = 0;
        $total = 0;
        
        if ($c3 > 0 || JawabanPraktikum::where('murid_id', $muridId)->exists()) { $total += $c3; $bobot++; }
        if ($c4 > 0) { $total += $c4; $bobot++; }
        if ($c5 > 0) { $total += $c5; $bobot++; }
        
        return $bobot > 0 ? round($total / $bobot, 2) : 0;
    }
}
