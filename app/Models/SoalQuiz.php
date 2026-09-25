<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LevelBloom;
use App\Enums\TipeSoal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalQuiz extends Model
{
    use HasFactory;

    protected $table = 'soal_quiz';

    protected $fillable = [
        'quiz_id', 'pertanyaan', 'tipe', 'opsi', 'poin_dasar',
        'level_bloom', 'skor_maks', 'kunci_jawaban', 'rubrik',
        'pembahasan', 'hint', 'urutan', 'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tipe'          => TipeSoal::class,
            'level_bloom'   => LevelBloom::class,
            'opsi'          => 'array',
            'kunci_jawaban' => 'array',
            'rubrik'        => 'array',
            'is_aktif'      => 'boolean',
        ];
    }

    public function scopeAktif(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_aktif', true);
    }

    public function quiz(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function jawabanQuizzes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JawabanQuiz::class);
    }
}
