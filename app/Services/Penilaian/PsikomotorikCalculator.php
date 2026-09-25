<?php

declare(strict_types=1);

namespace App\Services\Penilaian;

use App\Models\PenilaianPraktikum;

class PsikomotorikCalculator
{
    /**
     * Menghitung nilai psikomotorik dari satu PenilaianPraktikum.
     * Mengembalikan persentase (0-100).
     */
    public function hitung(int $penilaianPraktikumId): float
    {
        $penilaian = PenilaianPraktikum::with('nilaiAspeks')->find($penilaianPraktikumId);
        
        if (!$penilaian || $penilaian->nilaiAspeks->isEmpty()) {
            return 0.0;
        }

        $jumlahAspek = $penilaian->nilaiAspeks->count();
        $totalSkor = $penilaian->nilaiAspeks->sum('skor');
        
        $skorMaksimal = 4 * $jumlahAspek; // Skala 1-4 per aspek

        if ($skorMaksimal == 0) return 0.0;

        return round(($totalSkor / $skorMaksimal) * 100, 2);
    }
    
    /**
     * Menghitung rata-rata nilai psikomotorik untuk seluruh praktikum seorang murid
     */
    public function hitungRataRata(int $muridId): float
    {
        $penilaians = PenilaianPraktikum::where('murid_id', $muridId)->pluck('id');
        
        if ($penilaians->isEmpty()) {
            return 0.0;
        }
        
        $totalPersen = 0;
        foreach ($penilaians as $id) {
            $totalPersen += $this->hitung($id);
        }
        
        return round($totalPersen / $penilaians->count(), 2);
    }
}
