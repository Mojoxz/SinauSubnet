<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Materi;
use App\Models\User;

class MateriPolicy
{
    /** Guru bisa melihat semua materi. Murid hanya melihat yang is_aktif. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Materi $materi): bool
    {
        return $user->isGuru() || $materi->is_aktif;
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Materi $materi): bool
    {
        return $user->isGuru();
    }

    /**
     * Hapus materi. Jika sudah ada jawaban murid di anak-anaknya,
     * operasi ini DITOLAK — soft-archive (is_aktif = false) wajib dilakukan
     * oleh controller/action sebelum memanggil Policy ini.
     */
    public function delete(User $user, Materi $materi): bool
    {
        return $user->isGuru();
    }

    public function forceDelete(User $user, Materi $materi): bool
    {
        return false; // Tidak ada force delete
    }
}
