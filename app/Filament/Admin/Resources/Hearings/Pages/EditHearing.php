<?php

namespace App\Filament\Admin\Resources\Hearings\Pages;

use App\Filament\Admin\Resources\Hearings\HearingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHearing extends EditRecord
{
    protected static string $resource = HearingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
