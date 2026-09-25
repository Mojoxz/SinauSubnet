<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Materi;
use App\Models\Murid;
use App\Models\Progress;

class UpdateProgressPblAction
{
    /**
     * Memperbarui progress tahap PBL (Materi, Praktikum, atau Quiz).
     * @param string $tahap Enum virtual: 'materi' | 'praktikum' | 'quiz'
     * @param int $persentase 0-100
     */
    public function execute(Murid $murid, Materi $materi, string $tahap, int $persentase): void
    {
        $progress = Progress::firstOrCreate([
            'murid_id' => $murid->id,
            'materi_id' => $materi->id,
        ], [
            'persen_materi' => 0,
            'persen_praktikum' => 0,
            'persen_quiz' => 0,
            'terakhir_diperbarui_pada' => now(),
        ]);

        $kolom = "persen_{$tahap}"; // persen_materi, persen_praktikum, persen_quiz
        
        $progress->update([
            $kolom => $persentase,
            'terakhir_diperbarui_pada' => now(),
        ]);
    }
}
