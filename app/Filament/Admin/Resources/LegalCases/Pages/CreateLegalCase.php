<?php

namespace App\Filament\Admin\Resources\LegalCases\Pages;

use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalCase extends CreateRecord
{
    protected static string $resource = LegalCaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
