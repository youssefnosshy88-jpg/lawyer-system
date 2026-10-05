<?php

namespace App\Filament\Admin\Resources\CaseTypes\Pages;

use App\Filament\Admin\Resources\CaseTypes\CaseTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCaseTypes extends ListRecords
{
    protected static string $resource = CaseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
