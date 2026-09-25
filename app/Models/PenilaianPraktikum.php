<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianPraktikum extends Model
{
    protected $table = 'penilaian_praktikum';
    protected $fillable = ['praktikum_id', 'murid_id', 'guru_id', 'catatan'];

    public function praktikum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function murid(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Murid::class);
    }

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function nilaiAspeks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NilaiAspek::class);
    }
}
