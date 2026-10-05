<?php

namespace App\Filament\Admin\Resources\LegalCases\Pages;

use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLegalCase extends EditRecord
{
    protected static string $resource = LegalCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
