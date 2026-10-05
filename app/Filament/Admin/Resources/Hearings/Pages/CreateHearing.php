<?php

namespace App\Filament\Admin\Resources\Hearings\Pages;

use App\Filament\Admin\Resources\Hearings\HearingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHearing extends CreateRecord
{
    protected static string $resource = HearingResource::class;
}
