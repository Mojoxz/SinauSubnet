<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Praktikum extends Model
{
    use HasFactory;

    protected $table = 'praktikum';

    protected $fillable = ['materi_id', 'judul', 'studi_kasus', 'is_aktif'];

    protected function casts(): array
    {
        return ['is_aktif' => 'boolean'];
    }

    public function scopeAktif(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_aktif', true);
    }

    public function materi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    public function soalPraktikums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SoalPraktikum::class);
    }

    public function rubrikPraktikums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RubrikPraktikum::class)->orderBy('urutan');
    }

    public function penilaianPraktikums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenilaianPraktikum::class);
    }

    public function memilikiJawabanMurid(): bool
    {
        return $this->soalPraktikums()->whereHas('jawabanPraktikums')->exists();
    }
}

