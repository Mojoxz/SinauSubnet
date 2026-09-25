<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Badge;
use App\Models\Murid;

class CheckBadgeEligibilityAction
{
    /**
     * Memeriksa seluruh badge yang belum dimiliki murid,
     * apakah syarat poinnya sudah terpenuhi.
     */
    public function execute(Murid $murid): void
    {
        $totalPoin = $murid->total_poin;

        // Ambil badge yang belum dimiliki murid, tapi syarat poinnya terpenuhi
        $badgesTersedia = Badge::where('syarat_poin', '<=', $totalPoin)
            ->whereNotIn('id', $murid->badges()->pluck('badges.id'))
            ->get();

        if ($badgesTersedia->isNotEmpty()) {
            $syncData = [];
            foreach ($badgesTersedia as $badge) {
                $syncData[$badge->id] = ['diperoleh_pada' => now()];
            }
            
            // Menggunakan syncWithoutDetaching agar badge yang sudah ada tidak terhapus
            $murid->badges()->syncWithoutDetaching($syncData);
        }
    }
}
