<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CompanyStatus: string implements HasLabel, HasColor
{
    use TranslatableEnum;

    case UNDER_INCORPORATION = 'under_incorporation';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case UNDER_LIQUIDATION = 'under_liquidation';
    case DISSOLVED = 'dissolved';

    public function getColor(): string
    {
        return match ($this) {
            self::UNDER_INCORPORATION => 'info',
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
            self::UNDER_LIQUIDATION => 'warning',
            self::DISSOLVED => 'danger',
        };
    }
}
