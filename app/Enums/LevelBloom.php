<?php

declare(strict_types=1);

namespace App\Enums;

enum LevelBloom: string
{
    case C3 = 'C3';
    case C4 = 'C4';
    case C5 = 'C5';

    public function label(): string
    {
        return match ($this) {
            self::C3 => 'C3 — Menerapkan',
            self::C4 => 'C4 — Menganalisis',
            self::C5 => 'C5 — Mengevaluasi',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::C3 => 'bg-sky-100 text-sky-800 border-sky-300',
            self::C4 => 'bg-violet-100 text-violet-800 border-violet-300',
            self::C5 => 'bg-orange-100 text-orange-800 border-orange-300',
        };
    }
}
