<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Praktikum;
use App\Models\User;

class PraktikumPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Praktikum $praktikum): bool
    {
        return $user->isGuru() || $praktikum->is_aktif;
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Praktikum $praktikum): bool
    {
        return $user->isGuru();
    }

    /**
     * Hard delete diblokir jika sudah ada jawaban murid.
     * Controller harus mengarahkan ke soft-archive (is_aktif = false).
     */
    public function delete(User $user, Praktikum $praktikum): bool
    {
        if (! $user->isGuru()) {
            return false;
        }

        return ! $praktikum->memilikiJawabanMurid();
    }
}
