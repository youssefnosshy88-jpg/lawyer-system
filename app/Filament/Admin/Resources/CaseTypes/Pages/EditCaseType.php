<?php

namespace App\Filament\Admin\Resources\CaseTypes\Pages;

use App\Filament\Admin\Resources\CaseTypes\CaseTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCaseType extends EditRecord
{
    protected static string $resource = CaseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
