<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum PartnerRole: string implements HasLabel
{
    use TranslatableEnum;

    case PARTNER = 'partner';
    case SHAREHOLDER = 'shareholder';
    case MANAGER = 'manager';
    case CHAIRMAN = 'chairman';
    case BOARD_MEMBER = 'board_member';
    case MANAGING_PARTNER = 'managing_partner';
    case LEGAL_REPRESENTATIVE = 'legal_representative';
}
