<?php

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\CreateRole;
use App\Filament\Admin\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Resources\Roles\Pages\ListRoles;
use App\Support\Permissions;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $slug = 'roles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 81;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.settings');
    }

    public static function getModelLabel(): string
    {
        return __('roles.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('roles.plural');
    }

    public static function form(Schema $schema): Schema
    {
        $sections = [
            TextInput::make('name')->label(__('roles.fields.name'))->required()->unique(ignoreRecord: true)->disabled(fn ($record) => $record && in_array($record->name, ['admin', 'client'], true)),
        ];

        foreach (Permissions::RESOURCES as $resource) {
            $sections[] = Section::make(__("permissions.resources.{$resource}"))
                ->compact()
                ->collapsible()
                ->schema([
                    CheckboxList::make("permissions_{$resource}")
                        ->hiddenLabel()
                        ->options(collect(Permissions::ACTIONS)->mapWithKeys(fn ($a) => ["{$a}_{$resource}" => __("permissions.actions.{$a}")]))
                        ->columns(5)
                        ->bulkToggleable()
                        ->afterStateHydrated(function (CheckboxList $component, $record) use ($resource): void {
                            if ($record) {
                                $component->state($record->permissions->pluck('name')->filter(fn ($n) => str_ends_with($n, "_{$resource}"))->values()->all());
                            }
                        })
                        ->dehydrated(false),
                ]);
        }

        return $schema->components($sections)->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('roles.fields.name'))->formatStateUsing(fn ($state) => __("roles.{$state}"))->weight('bold'),
                TextColumn::make('permissions_count')->label(__('roles.fields.permissions_count'))->counts('permissions')->alignCenter(),
                TextColumn::make('users_count')->label(__('roles.fields.users_count'))->counts('users')->alignCenter(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (Role $r) => in_array($r->name, ['admin', 'lawyer', 'secretary', 'accountant', 'client'], true)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
