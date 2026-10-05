<?php

namespace App\Enums;

use App\Enums\Concerns\TranslatableEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProcedureStatus: string implements HasLabel, HasColor
{
    use TranslatableEnum;

    case DRAFT = 'draft';
    case DOCUMENTS_PENDING = 'documents_pending';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::DOCUMENTS_PENDING => 'warning',
            self::SUBMITTED => 'info',
            self::UNDER_REVIEW => 'info',
            self::APPROVED => 'success',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
