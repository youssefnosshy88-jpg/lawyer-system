<?php

namespace App\Filament\Admin\Resources\FeeAgreements\Pages;

use App\Filament\Admin\Resources\FeeAgreements\FeeAgreementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeeAgreement extends EditRecord
{
    protected static string $resource = FeeAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
