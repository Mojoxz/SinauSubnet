<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    protected $fillable = ['guru_id', 'level', 'judul', 'konten', 'urutan', 'is_aktif'];

    protected function casts(): array
    {
        return ['is_aktif' => 'boolean'];
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeAktif(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_aktif', true);
    }

    // ─── Relasi ───────────────────────────────────────────────

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function praktikums(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Praktikum::class);
    }

    public function quizzes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function progresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Cek apakah materi ini (atau anak-anaknya) sudah memiliki jawaban murid.
     * Dipakai untuk logika soft-archive berjenjang di MateriPolicy.
     */
    public function memilikiJawabanMurid(): bool
    {
        $adaJawabanPraktikum = $this->praktikums()
            ->whereHas('soalPraktikums.jawabanPraktikums')
            ->exists();

        $adaJawabanQuiz = $this->quizzes()
            ->whereHas('hasilQuizzes')
            ->exists();

        return $adaJawabanPraktikum || $adaJawabanQuiz;
    }
}
