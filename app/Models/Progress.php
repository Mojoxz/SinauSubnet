<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progress';

    protected $fillable = [
        'murid_id', 'materi_id',
        'persen_materi', 'persen_praktikum', 'persen_quiz',
        'terakhir_diperbarui_pada',
    ];

    protected function casts(): array
    {
        return ['terakhir_diperbarui_pada' => 'datetime'];
    }

    public function murid(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Murid::class);
    }

    public function materi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Materi::class);
    }

    /**
     * Hitung rata-rata progress PBL (Materi 33% + Praktikum 33% + Quiz 33%).
     */
    public function persenTotal(): int
    {
        return (int) round(($this->persen_materi + $this->persen_praktikum + $this->persen_quiz) / 3);
    }
}
