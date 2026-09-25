<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quiz';

    protected $fillable = ['materi_id', 'judul', 'durasi_menit', 'bonus_kecepatan_maks', 'is_aktif'];

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

    public function soalQuizzes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SoalQuiz::class)->orderBy('urutan');
    }

    public function hasilQuizzes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HasilQuiz::class);
    }

    public function memilikiJawabanMurid(): bool
    {
        return $this->hasilQuizzes()->exists();
    }
}
