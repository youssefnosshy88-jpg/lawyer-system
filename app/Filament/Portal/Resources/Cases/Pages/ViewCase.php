<?php

namespace App\Filament\Portal\Resources\Cases\Pages;

use App\Filament\Portal\Resources\Cases\CaseResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCase extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function mount(int|string $record): void
    {
        abort_unless(
            CaseResource::getEloquentQuery()->whereKey($record)->exists(),
            404,
        );

        parent::mount($record);
    }
}
