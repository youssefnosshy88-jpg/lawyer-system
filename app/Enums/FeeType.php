<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum FeeType: string implements HasLabel
{
    use TranslatableEnum;

    case FIXED = 'fixed';
    case HOURLY = 'hourly';
    case PERCENTAGE = 'percentage';
    case INSTALLMENTS = 'installments';
    case RETAINER = 'retainer';
}
