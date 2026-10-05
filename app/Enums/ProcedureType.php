<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum ProcedureType: string implements HasLabel
{
    use TranslatableEnum;

    case INCORPORATION = 'incorporation';
    case AMEND_ARTICLES = 'amend_articles';
    case CAPITAL_INCREASE = 'capital_increase';
    case CAPITAL_DECREASE = 'capital_decrease';
    case CHANGE_MANAGER = 'change_manager';
    case CHANGE_ADDRESS = 'change_address';
    case ADD_ACTIVITY = 'add_activity';
    case REGISTER_RENEWAL = 'register_renewal';
    case GAFI_LICENSE = 'gafi_license';
    case GENERAL_ASSEMBLY = 'general_assembly';
    case BRANCH_OPENING = 'branch_opening';
    case TRANSFER_SHARES = 'transfer_shares';
    case LIQUIDATION = 'liquidation';
    case OTHER = 'other';
}
