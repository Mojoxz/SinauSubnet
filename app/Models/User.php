<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    // Tabel 'users' dipertahankan jamak (pengecualian — lihat docs/KEPUTUSAN.md)

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────

    public function guru(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function murid(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Murid::class);
    }

    // ─── Helper ───────────────────────────────────────────────

    public function isGuru(): bool
    {
        return $this->hasRole(RoleUser::GURU->value);
    }

    public function isMurid(): bool
    {
        return $this->hasRole(RoleUser::MURID->value);
    }
}
