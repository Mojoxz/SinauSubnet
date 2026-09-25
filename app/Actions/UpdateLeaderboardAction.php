<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Murid;
use App\Models\RiwayatPoin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UpdateLeaderboardAction
{
    public function __construct(
        private CheckBadgeEligibilityAction $badgeAction
    ) {}

    /**
     * Memperbarui poin murid dengan transaksi aman untuk menghindari duplikasi.
     * 
     * @param Murid $murid Murid yang mendapat poin
     * @param Model $sumber Model asal (mis. HasilQuiz)
     * @param int $poin Tambahan poin
     * @param string $keterangan Deskripsi
     */
    public function execute(Murid $murid, Model $sumber, int $poin, string $keterangan): void
    {
        if ($poin <= 0) return;

        DB::transaction(function () use ($murid, $sumber, $poin, $keterangan) {
            // Gunakan firstOrCreate untuk idempotensi (anti-duplikat jika queue di-retry)
            $riwayat = RiwayatPoin::firstOrCreate([
                'murid_id' => $murid->id,
                'sumber_type' => $sumber->getMorphClass(),
                'sumber_id' => $sumber->getKey(),
            ], [
                'poin' => $poin,
                'keterangan' => $keterangan,
            ]);

            // Jika row benar-benar baru saja dibuat (wasRecentlyCreated), update cache di Murid
            if ($riwayat->wasRecentlyCreated) {
                // Kalkulasi ulang dari riwayat untuk jaminan akurasi, alih-alih murid->increment()
                $totalPoinBaru = RiwayatPoin::where('murid_id', $murid->id)->sum('poin');
                
                $murid->update([
                    'total_poin' => $totalPoinBaru,
                    'poin_dicapai_pada' => now(), // Tie breaker waktu
                ]);

                // Periksa apakah pantas mendapat badge baru
                $this->badgeAction->execute($murid);
            }
        });
    }
}
