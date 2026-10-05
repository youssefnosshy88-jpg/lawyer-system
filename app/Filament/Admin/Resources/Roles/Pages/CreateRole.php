<?php

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Support\Permissions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guard_name'] = 'web';

        return $data;
    }

    protected function afterCreate(): void
    {
        $permissions = [];

        foreach (Permissions::RESOURCES as $resource) {
            $permissions = array_merge($permissions, $this->data["permissions_{$resource}"] ?? []);
        }

        $this->getRecord()->syncPermissions($permissions);
    }
}
