<?php

namespace App\Filament\Admin\Resources\CaseTypes;

use App\Filament\Admin\Resources\CaseTypes\Pages\CreateCaseType;
use App\Filament\Admin\Resources\CaseTypes\Pages\EditCaseType;
use App\Filament\Admin\Resources\CaseTypes\Pages\ListCaseTypes;
use App\Models\CaseType;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CaseTypeResource extends Resource
{
    protected static ?string $model = CaseType::class;

    protected static ?string $slug = 'case-types';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 91;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.settings');
    }

    public static function getModelLabel(): string
    {
        return __('case_types.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('case_types.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('courts.fields.name'))->required(),
            TextInput::make('name_en')->label(__('courts.fields.name_en')),
            Select::make('color')->label(__('case_types.fields.color'))->options([
                'primary' => 'Primary', 'success' => 'Success', 'warning' => 'Warning', 'danger' => 'Danger', 'info' => 'Info', 'gray' => 'Gray',
            ])->default('gray'),
            Toggle::make('is_active')->label(__('app.fields.is_active'))->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('courts.fields.name'))->badge()->color(fn (CaseType $r) => $r->color ?? 'gray')->searchable(),
                TextColumn::make('name_en')->label(__('courts.fields.name_en')),
                TextColumn::make('cases_count')->label(__('clients.fields.cases_count'))->counts('cases')->alignCenter(),
                IconColumn::make('is_active')->label(__('app.fields.is_active'))->boolean(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseTypes::route('/'),
            'create' => CreateCaseType::route('/create'),
            'edit' => EditCaseType::route('/{record}/edit'),
        ];
    }
}
