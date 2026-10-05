<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CaseStatus: string implements HasLabel, HasColor
{
    use TranslatableEnum;

    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case POSTPONED = 'postponed';
    case JUDGED = 'judged';
    case APPEALED = 'appealed';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    public function getColor(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::IN_PROGRESS => 'warning',
            self::POSTPONED => 'gray',
            self::JUDGED => 'success',
            self::APPEALED => 'warning',
            self::CLOSED => 'gray',
            self::ARCHIVED => 'gray',
        };
    }
}
