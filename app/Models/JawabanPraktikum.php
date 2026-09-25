<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPenilaian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanPraktikum extends Model
{
    use HasFactory;

    protected $table = 'jawaban_praktikum';

    protected $fillable = [
        'soal_praktikum_id', 'murid_id', 'jawaban', 'file_bukti_path',
        'skor_ai', 'skor_final', 'feedback_ai', 'feedback_final',
        'status_penilaian', 'detail_skor_ai', 'jumlah_percobaan_ai',
        'sampel_review', 'ditinjau_oleh', 'ditinjau_pada',
    ];

    protected function casts(): array
    {
        return [
            'status_penilaian' => StatusPenilaian::class,
            'detail_skor_ai'   => 'array',
            'sampel_review'    => 'boolean',
            'ditinjau_pada'    => 'datetime',
        ];
    }

    public function soalPraktikum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SoalPraktikum::class);
    }

    public function murid(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Murid::class);
    }

    public function peninjau(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class, 'ditinjau_oleh');
    }
}
