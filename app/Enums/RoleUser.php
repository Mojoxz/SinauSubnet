<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleUser: string
{
    case GURU  = 'guru';
    case MURID = 'murid';

    public function label(): string
    {
        return match ($this) {
            self::GURU  => 'Guru',
            self::MURID => 'Murid',
        };
    }
}
