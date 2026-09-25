<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HasilQuiz;
use App\Models\User;

class HasilQuizPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Murid hanya melihat hasil miliknya sendiri. Guru melihat semua. */
    public function view(User $user, HasilQuiz $hasil): bool
    {
        if ($user->isGuru()) {
            return true;
        }

        return $user->murid?->id === $hasil->murid_id;
    }

    public function create(User $user): bool
    {
        return $user->isMurid();
    }

    public function update(User $user, HasilQuiz $hasil): bool
    {
        return $user->isGuru();
    }

    public function delete(User $user, HasilQuiz $hasil): bool
    {
        return false; // Hasil kuis tidak boleh dihapus
    }
}
