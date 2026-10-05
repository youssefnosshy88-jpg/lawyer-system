<?php

namespace App\Filament\Admin\Resources\CaseTypes\Pages;

use App\Filament\Admin\Resources\CaseTypes\CaseTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCaseType extends CreateRecord
{
    protected static string $resource = CaseTypeResource::class;
}
