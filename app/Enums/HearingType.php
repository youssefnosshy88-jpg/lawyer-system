<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum HearingType: string implements HasLabel
{
    use TranslatableEnum;

    case PLEADING = 'pleading';
    case EVIDENCE = 'evidence';
    case EXPERT_REPORT = 'expert_report';
    case JUDGMENT = 'judgment';
    case PROCEDURAL = 'procedural';
    case EXECUTION = 'execution';
    case OTHER = 'other';
}
