<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RubrikPraktikum extends Model
{
    protected $table = 'rubrik_praktikum';
    protected $fillable = ['praktikum_id', 'aspek', 'indikator', 'urutan'];

    public function praktikum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function nilaiAspeks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NilaiAspek::class);
    }
}
