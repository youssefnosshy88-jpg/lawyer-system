<?php

namespace App\Filament\Admin\Resources\Courts;

use App\Filament\Admin\Resources\Courts\Pages\CreateCourt;
use App\Filament\Admin\Resources\Courts\Pages\EditCourt;
use App\Filament\Admin\Resources\Courts\Pages\ListCourts;
use App\Models\Court;
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

class CourtResource extends Resource
{
    protected static ?string $model = Court::class;

    protected static ?string $slug = 'courts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?int $navigationSort = 90;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.settings');
    }

    public static function getModelLabel(): string
    {
        return __('courts.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('courts.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('courts.fields.name'))->required(),
            TextInput::make('name_en')->label(__('courts.fields.name_en')),
            Select::make('type')->label(__('courts.fields.type'))->options(__('courts.types')),
            TextInput::make('city')->label(__('clients.fields.city')),
            TextInput::make('address')->label(__('clients.fields.address')),
            TextInput::make('phone')->label(__('clients.fields.phone'))->tel(),
            Toggle::make('is_active')->label(__('app.fields.is_active'))->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('courts.fields.name'))->searchable()->sortable()->description(fn (Court $r) => $r->name_en),
                TextColumn::make('type')->label(__('courts.fields.type'))->formatStateUsing(fn ($state) => __('courts.types')[$state] ?? $state)->badge()->color('gray'),
                TextColumn::make('city')->label(__('clients.fields.city'))->searchable(),
                TextColumn::make('cases_count')->label(__('clients.fields.cases_count'))->counts('cases')->alignCenter(),
                IconColumn::make('is_active')->label(__('app.fields.is_active'))->boolean(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourts::route('/'),
            'create' => CreateCourt::route('/create'),
            'edit' => EditCourt::route('/{record}/edit'),
        ];
    }
}
