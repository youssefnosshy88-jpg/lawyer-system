<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClientType: string implements HasLabel, HasColor
{
    use TranslatableEnum;

    case INDIVIDUAL = 'individual';
    case COMPANY = 'company';

    public function getColor(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'info',
            self::COMPANY => 'primary',
        };
    }
}
