<?php

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Support\Permissions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $permissions = [];

        foreach (Permissions::RESOURCES as $resource) {
            $permissions = array_merge($permissions, $this->data["permissions_{$resource}"] ?? []);
        }

        $this->getRecord()->syncPermissions($permissions);
    }
}
