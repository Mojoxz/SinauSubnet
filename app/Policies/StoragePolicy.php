<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JawabanPraktikum;
use App\Models\User;

/**
 * StoragePolicy — otorisasi streaming/download berkas bukti praktik
 * dari private storage (screenshot JPG/PNG dan file .pkt/.pka).
 *
 * Digunakan di controller yang meng-stream file via:
 *   Storage::disk('local')->download($path);
 */
class StoragePolicy
{
    /**
     * Murid hanya bisa mengakses file yang ia unggah sendiri.
     * Guru bisa mengakses semua file untuk keperluan penilaian.
     */
    public function downloadBuktiFile(User $user, JawabanPraktikum $jawaban): bool
    {
        if ($user->isGuru()) {
            return true;
        }

        return $user->murid?->id === $jawaban->murid_id;
    }
}
