<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 80;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.settings');
    }

    public static function getModelLabel(): string
    {
        return __('users.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('users.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->label(__('users.fields.name'))->required(),
                TextInput::make('email')->label(__('users.fields.email'))->email()->required()->unique(ignoreRecord: true),
                TextInput::make('phone')->label(__('clients.fields.phone'))->tel(),
                TextInput::make('job_title')->label(__('users.fields.job_title')),
                TextInput::make('bar_registration_no')->label(__('users.fields.bar_registration_no')),
                Select::make('locale')->label(__('users.fields.locale'))->options(config('app.available_locales'))->default('ar'),
                Select::make('roles')
                    ->label(__('users.fields.roles'))
                    ->relationship('roles', 'name', fn (Builder $q) => $q->whereIn('name', User::STAFF_ROLES))
                    ->getOptionLabelFromRecordUsing(fn ($record) => __("roles.{$record->name}"))
                    ->multiple()
                    ->preload()
                    ->required(),
                TextInput::make('password')
                    ->label(__('users.fields.password'))
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8),
                Toggle::make('is_active')->label(__('app.fields.is_active'))->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->whereNull('client_id')->with('roles'))
            ->columns([
                TextColumn::make('name')->label(__('users.fields.name'))->searchable()->sortable()->weight('bold'),
                TextColumn::make('email')->label(__('users.fields.email'))->searchable(),
                TextColumn::make('job_title')->label(__('users.fields.job_title')),
                TextColumn::make('roles.name')->label(__('users.fields.roles'))->badge()->formatStateUsing(fn ($state) => __("roles.{$state}")),
                IconColumn::make('is_active')->label(__('app.fields.is_active'))->boolean(),
            ])
            ->filters([
                SelectFilter::make('roles')->label(__('users.fields.roles'))->relationship('roles', 'name')->getOptionLabelFromRecordUsing(fn ($record) => __("roles.{$record->name}")),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()->hidden(fn (User $r) => $r->id === auth()->id())]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
