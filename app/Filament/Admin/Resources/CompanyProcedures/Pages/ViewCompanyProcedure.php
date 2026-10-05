<?php

namespace App\Filament\Admin\Resources\CompanyProcedures\Pages;

use App\Filament\Admin\Resources\CompanyProcedures\CompanyProcedureResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyProcedure extends ViewRecord
{
    protected static string $resource = CompanyProcedureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
