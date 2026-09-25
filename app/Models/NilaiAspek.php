<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiAspek extends Model
{
    protected $table = 'nilai_aspek';
    protected $fillable = ['penilaian_praktikum_id', 'rubrik_praktikum_id', 'skor'];

    public function penilaianPraktikum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PenilaianPraktikum::class);
    }

    public function rubrikPraktikum(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RubrikPraktikum::class);
    }
}
