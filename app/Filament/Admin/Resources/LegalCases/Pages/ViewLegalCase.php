<?php

namespace App\Filament\Admin\Resources\LegalCases\Pages;

use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLegalCase extends ViewRecord
{
    protected static string $resource = LegalCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
