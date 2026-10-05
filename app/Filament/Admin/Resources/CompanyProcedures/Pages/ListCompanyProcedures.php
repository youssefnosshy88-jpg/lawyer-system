<?php

namespace App\Filament\Admin\Resources\CompanyProcedures\Pages;

use App\Filament\Admin\Resources\CompanyProcedures\CompanyProcedureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyProcedures extends ListRecords
{
    protected static string $resource = CompanyProcedureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
