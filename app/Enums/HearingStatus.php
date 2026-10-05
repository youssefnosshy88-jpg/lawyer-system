<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum HearingStatus: string implements HasLabel, HasColor
{
    use TranslatableEnum;

    case SCHEDULED = 'scheduled';
    case HELD = 'held';
    case POSTPONED = 'postponed';
    case CANCELLED = 'cancelled';

    public function getColor(): string
    {
        return match ($this) {
            self::SCHEDULED => 'info',
            self::HELD => 'success',
            self::POSTPONED => 'warning',
            self::CANCELLED => 'danger',
        };
    }
}
