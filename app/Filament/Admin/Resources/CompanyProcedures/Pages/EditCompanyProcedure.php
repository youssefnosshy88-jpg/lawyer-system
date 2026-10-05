<?php

namespace App\Filament\Admin\Resources\CompanyProcedures\Pages;

use App\Filament\Admin\Resources\CompanyProcedures\CompanyProcedureResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyProcedure extends EditRecord
{
    protected static string $resource = CompanyProcedureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
