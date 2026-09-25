<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Murid hanya melihat feedback yang ditujukan kepadanya. */
    public function view(User $user, Feedback $feedback): bool
    {
        if ($user->isGuru()) {
            return true;
        }

        return $user->murid?->id === $feedback->murid_id;
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Feedback $feedback): bool
    {
        return $user->isGuru();
    }

    public function delete(User $user, Feedback $feedback): bool
    {
        return $user->isGuru();
    }
}
