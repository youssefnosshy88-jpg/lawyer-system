<?php

namespace App\Filament\Admin\Resources\FeeAgreements\Pages;

use App\Filament\Admin\Resources\FeeAgreements\FeeAgreementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeeAgreements extends ListRecords
{
    protected static string $resource = FeeAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
