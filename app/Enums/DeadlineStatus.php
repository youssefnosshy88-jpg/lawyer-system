<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DeadlineStatus: string implements HasLabel, HasColor
{
    use TranslatableEnum;

    case PENDING = 'pending';
    case DONE = 'done';
    case OVERDUE = 'overdue';

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::DONE => 'success',
            self::OVERDUE => 'danger',
        };
    }
}
