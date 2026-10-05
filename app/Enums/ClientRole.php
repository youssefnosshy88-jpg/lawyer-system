<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum ClientRole: string implements HasLabel
{
    use TranslatableEnum;

    case PLAINTIFF = 'plaintiff';
    case DEFENDANT = 'defendant';
    case APPELLANT = 'appellant';
    case RESPONDENT = 'respondent';
    case PETITIONER = 'petitioner';
    case OTHER = 'other';
}
