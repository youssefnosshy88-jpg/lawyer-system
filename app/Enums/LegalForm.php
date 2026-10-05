<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum LegalForm: string implements HasLabel
{
    use TranslatableEnum;

    case LLC = 'llc';
    case JSC = 'jsc';
    case ONE_PERSON = 'one_person';
    case GENERAL_PARTNERSHIP = 'general_partnership';
    case LIMITED_PARTNERSHIP = 'limited_partnership';
    case PARTNERSHIP_LIMITED_BY_SHARES = 'partnership_limited_by_shares';
    case BRANCH = 'branch';
    case REPRESENTATIVE_OFFICE = 'representative_office';
    case SOLE_PROPRIETORSHIP = 'sole_proprietorship';
}
