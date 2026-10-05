<?php

namespace App\Filament\Admin\Resources\CompanyDeadlines\Pages;

use App\Filament\Admin\Resources\CompanyDeadlines\CompanyDeadlineResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyDeadline extends EditRecord
{
    protected static string $resource = CompanyDeadlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
