<?php

namespace App\Filament\Admin\Resources\CompanyDeadlines\Pages;

use App\Filament\Admin\Resources\CompanyDeadlines\CompanyDeadlineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyDeadlines extends ListRecords
{
    protected static string $resource = CompanyDeadlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
