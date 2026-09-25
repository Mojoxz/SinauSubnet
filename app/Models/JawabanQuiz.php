<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPenilaian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JawabanQuiz extends Model
{
    use HasFactory;

    protected $table = 'jawaban_quiz';

    protected $fillable = [
        'hasil_quiz_id', 'soal_quiz_id', 'jawaban',
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

    public function hasilQuiz(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HasilQuiz::class);
    }

    public function soalQuiz(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SoalQuiz::class);
    }

    public function peninjau(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class, 'ditinjau_oleh');
    }
}
