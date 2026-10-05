<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasLabel;

enum CaseDegree: string implements HasLabel
{
    use TranslatableEnum;

    case FIRST_INSTANCE = 'first_instance';
    case APPEAL = 'appeal';
    case CASSATION = 'cassation';
    case SUPREME_CONSTITUTIONAL = 'supreme_constitutional';
    case ADMINISTRATIVE = 'administrative';
    case EXECUTION = 'execution';
}
