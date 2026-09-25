<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quiz $quiz): bool
    {
        return $user->isGuru() || $quiz->is_aktif;
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Quiz $quiz): bool
    {
        return $user->isGuru();
    }

    /**
     * Hard delete diblokir jika kuis sudah memiliki hasil (jawaban murid).
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        if (! $user->isGuru()) {
            return false;
        }

        return ! $quiz->memilikiJawabanMurid();
    }
}
