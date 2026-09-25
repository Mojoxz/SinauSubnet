<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LevelBloom;
use App\Enums\TipeSoal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalPraktikum extends Model
{
    use HasFactory;

    protected $table = 'soal_praktikum';

    protected $fillable = [
        'praktikum_id', 'pertanyaan', 'tipe', 'level_bloom',
        'skor_maks', 'kunci_jawaban', 'rubrik', 'pembahasan', 'hint', 'urutan', 'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tipe'          => TipeSoal::class,
            'level_bloom'   => LevelBloom::class,
            'kunci_jawaban' => 'array',
            'rubrik'        => 'array',
            'hint'          => 'array',
            'is_aktif'      => 'boolean',
        ];
    }

    public function scopeAktif(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_aktif', true);
    }

    public function praktikum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function jawabanPraktikums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JawabanPraktikum::class);
    }
}
