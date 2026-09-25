<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JawabanPraktikum;
use App\Models\User;

class JawabanPraktikumPolicy
{
    /** Murid hanya melihat jawaban miliknya. Guru melihat semua. */
    public function viewAny(User $user): bool
    {
        return true; // Filter diterapkan di query, bukan di sini
    }

    public function view(User $user, JawabanPraktikum $jawaban): bool
    {
        if ($user->isGuru()) {
            return true;
        }

        return $user->murid?->id === $jawaban->murid_id;
    }

    /** Murid hanya bisa menyimpan jawaban untuk soal yang belum pernah dijawab. */
    public function create(User $user): bool
    {
        return $user->isMurid();
    }

    /** Hanya guru yang boleh mengubah skor/feedback (override koreksi AI). */
    public function update(User $user, JawabanPraktikum $jawaban): bool
    {
        return $user->isGuru();
    }

    public function delete(User $user, JawabanPraktikum $jawaban): bool
    {
        return false; // Jawaban tidak boleh dihapus demi integritas data skripsi
    }
}
