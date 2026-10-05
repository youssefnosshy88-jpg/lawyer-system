<?php

namespace App\Filament\Admin\Resources\Opponents;

use App\Enums\ClientType;
use App\Filament\Admin\Resources\Opponents\Pages\CreateOpponent;
use App\Filament\Admin\Resources\Opponents\Pages\EditOpponent;
use App\Filament\Admin\Resources\Opponents\Pages\ListOpponents;
use App\Models\Opponent;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OpponentResource extends Resource
{
    protected static ?string $model = Opponent::class;

    protected static ?string $slug = 'opponents';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserMinus;

    protected static ?int $navigationSort = 12;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.clients');
    }

    public static function getModelLabel(): string
    {
        return __('opponents.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('opponents.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->label(__('clients.fields.type'))->options(ClientType::class)->default(ClientType::INDIVIDUAL)->required(),
            TextInput::make('name')->label(__('opponents.fields.name'))->required(),
            TextInput::make('national_id')->label(__('clients.fields.national_id')),
            TextInput::make('phone')->label(__('clients.fields.phone'))->tel(),
            TextInput::make('address')->label(__('clients.fields.address'))->columnSpanFull(),
            TextInput::make('lawyer_name')->label(__('opponents.fields.lawyer_name')),
            TextInput::make('lawyer_phone')->label(__('opponents.fields.lawyer_phone'))->tel(),
            Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('opponents.fields.name'))->searchable()->sortable()->weight('bold'),
                TextColumn::make('type')->label(__('clients.fields.type'))->badge()->color('gray'),
                TextColumn::make('phone')->label(__('clients.fields.phone')),
                TextColumn::make('lawyer_name')->label(__('opponents.fields.lawyer_name')),
                TextColumn::make('cases_count')->label(__('clients.fields.cases_count'))->counts('cases')->alignCenter(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOpponents::route('/'),
            'create' => CreateOpponent::route('/create'),
            'edit' => EditOpponent::route('/{record}/edit'),
        ];
    }
}
