<?php

namespace App\Filament\Portal\Resources\Hearings\Pages;

use App\Filament\Portal\Resources\Hearings\HearingResource;
use Filament\Resources\Pages\ListRecords;

class ListHearings extends ListRecords
{
    protected static string $resource = HearingResource::class;
}
