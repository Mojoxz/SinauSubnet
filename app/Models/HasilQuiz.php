<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilQuiz extends Model
{
    use HasFactory;

    protected $table = 'hasil_quiz';

    protected $fillable = [
        'quiz_id', 'murid_id', 'waktu_mulai', 'waktu_selesai', 'skor_total', 'poin_total',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai'   => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function quiz(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function murid(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Murid::class);
    }

    public function jawabanQuizzes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JawabanQuiz::class);
    }

    /**
     * Apakah kuis sudah selesai (waktu_selesai terisi)?
     */
    public function sudahSelesai(): bool
    {
        return $this->waktu_selesai !== null;
    }

    /**
     * Hitung sisa detik berdasarkan timer absolut server.
     * Negatif berarti waktu sudah habis.
     */
    public function sisaDetik(): int
    {
        $bataswaktu = $this->waktu_mulai->addMinutes($this->quiz->durasi_menit);
        return (int) now()->diffInSeconds($bataswaktu, false);
    }
}
