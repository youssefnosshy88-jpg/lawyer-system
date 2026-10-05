<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum ExpenseCategory: string implements HasLabel
{
    use TranslatableEnum;

    case COURT_FEES = 'court_fees';
    case EXPERT_FEES = 'expert_fees';
    case TRANSPORT = 'transport';
    case TRANSLATION = 'translation';
    case NOTARY = 'notary';
    case PUBLICATION = 'publication';
    case STAMPS = 'stamps';
    case OTHER = 'other';
}
