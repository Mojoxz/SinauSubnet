<?php

declare(strict_types=1);

namespace App\Enums;

enum TipeSoal: string
{
    case PILIHAN_GANDA = 'pilihan_ganda';
    case ISIAN         = 'isian';
    case URAIAN        = 'uraian';

    public function label(): string
    {
        return match ($this) {
            self::PILIHAN_GANDA => 'Pilihan Ganda',
            self::ISIAN         => 'Isian Singkat',
            self::URAIAN        => 'Uraian',
        };
    }

    /**
     * Apakah tipe soal ini dinilai secara otomatis-instan (bukan lewat AI)?
     */
    public function dinilaiOtomatis(): bool
    {
        return in_array($this, [self::PILIHAN_GANDA, self::ISIAN]);
    }

    /**
     * Apakah tipe soal ini menghasilkan poin gamifikasi?
     */
    public function menghasilkanPoin(): bool
    {
        return $this->dinilaiOtomatis();
    }
}
