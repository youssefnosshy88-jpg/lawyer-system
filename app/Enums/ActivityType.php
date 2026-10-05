<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum ActivityType: string implements HasLabel
{
    use TranslatableEnum;

    case MEMO = 'memo';
    case FILING = 'filing';
    case SERVICE = 'service';
    case MEETING = 'meeting';
    case CALL = 'call';
    case RESEARCH = 'research';
    case EXPERT = 'expert';
    case OTHER = 'other';
}
