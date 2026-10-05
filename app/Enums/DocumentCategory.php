<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum DocumentCategory: string implements HasLabel
{
    use TranslatableEnum;

    case PLEADING = 'pleading';
    case JUDGMENT = 'judgment';
    case CONTRACT = 'contract';
    case POWER_OF_ATTORNEY = 'power_of_attorney';
    case EVIDENCE = 'evidence';
    case CORRESPONDENCE = 'correspondence';
    case ID_DOCUMENT = 'id_document';
    case COMMERCIAL_REGISTER = 'commercial_register';
    case TAX_CARD = 'tax_card';
    case GAFI = 'gafi';
    case INVOICE = 'invoice';
    case OTHER = 'other';
}
