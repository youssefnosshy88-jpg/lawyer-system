<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum DeadlineType: string implements HasLabel
{
    use TranslatableEnum;

    case COMMERCIAL_REGISTER = 'commercial_register';
    case TAX_CARD = 'tax_card';
    case GAFI_LICENSE = 'gafi_license';
    case GENERAL_ASSEMBLY = 'general_assembly';
    case FINANCIAL_STATEMENTS = 'financial_statements';
    case SOCIAL_INSURANCE = 'social_insurance';
    case LICENSE = 'license';
    case OTHER = 'other';
}
