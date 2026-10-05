<?php

namespace App\Filament\Admin\Resources\Clients;

use App\Enums\ClientType;
use App\Filament\Admin\Resources\Clients\Pages\CreateClient;
use App\Filament\Admin\Resources\Clients\Pages\EditClient;
use App\Filament\Admin\Resources\Clients\Pages\ListClients;
use App\Filament\Admin\Resources\Clients\Pages\ViewClient;
use App\Filament\Admin\Resources\Clients\RelationManagers\CasesRelationManager;
use App\Filament\Admin\Resources\Clients\RelationManagers\CompaniesRelationManager;
use App\Filament\Admin\Resources\Clients\RelationManagers\ContactsRelationManager;
use App\Filament\Admin\Resources\Clients\RelationManagers\DocumentsRelationManager;
use App\Filament\Admin\Resources\Clients\RelationManagers\InvoicesRelationManager;
use App\Filament\Admin\Resources\Clients\RelationManagers\PowersOfAttorneyRelationManager;
use App\Models\Client;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $slug = 'clients';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.clients');
    }

    public static function getModelLabel(): string
    {
        return __('clients.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('clients.plural');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name', 'name_en', 'phone', 'national_id', 'email'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('clients.sections.basic'))
                ->columns(3)
                ->schema([
                    Select::make('type')
                        ->label(__('clients.fields.type'))
                        ->options(ClientType::class)
                        ->default(ClientType::INDIVIDUAL)
                        ->required()
                        ->live(),
                    TextInput::make('name')->label(__('clients.fields.name'))->required()->maxLength(255),
                    TextInput::make('name_en')->label(__('clients.fields.name_en'))->maxLength(255),
                    TextInput::make('national_id')
                        ->label(__('clients.fields.national_id'))
                        ->maxLength(30)
                        ->visible(fn (Get $get) => $get('type') === ClientType::INDIVIDUAL->value || $get('type') === ClientType::INDIVIDUAL),
                    TextInput::make('commercial_register_no')
                        ->label(__('clients.fields.commercial_register_no'))
                        ->visible(fn (Get $get) => $get('type') === ClientType::COMPANY->value || $get('type') === ClientType::COMPANY),
                    TextInput::make('tax_number')
                        ->label(__('clients.fields.tax_number'))
                        ->visible(fn (Get $get) => $get('type') === ClientType::COMPANY->value || $get('type') === ClientType::COMPANY),
                    TextInput::make('nationality')->label(__('clients.fields.nationality'))->default('مصري'),
                    TextInput::make('occupation')->label(__('clients.fields.occupation')),
                    Toggle::make('is_active')->label(__('app.fields.is_active'))->default(true)->inline(false),
                ]),
            Section::make(__('clients.sections.contact'))
                ->columns(3)
                ->schema([
                    TextInput::make('phone')->label(__('clients.fields.phone'))->tel()->required(),
                    TextInput::make('phone_alt')->label(__('clients.fields.phone_alt'))->tel(),
                    TextInput::make('email')->label(__('clients.fields.email'))->email(),
                    TextInput::make('city')->label(__('clients.fields.city')),
                    TextInput::make('address')->label(__('clients.fields.address'))->columnSpan(2),
                ]),
            Section::make(__('app.sections.notes'))
                ->collapsed()
                ->schema([
                    Textarea::make('notes')->label(__('app.fields.notes'))->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(__('clients.fields.code'))->searchable()->sortable()->weight('bold'),
                TextColumn::make('name')->label(__('clients.fields.name'))->searchable()->sortable()->description(fn (Client $r) => $r->name_en),
                TextColumn::make('type')->label(__('clients.fields.type'))->badge(),
                TextColumn::make('phone')->label(__('clients.fields.phone'))->searchable()->copyable(),
                TextColumn::make('cases_count')->label(__('clients.fields.cases_count'))->counts('cases')->alignCenter(),
                TextColumn::make('companies_count')->label(__('clients.fields.companies_count'))->counts('companies')->alignCenter()->toggleable(),
                IconColumn::make('is_active')->label(__('app.fields.is_active'))->boolean(),
                TextColumn::make('created_at')->label(__('app.fields.created_at'))->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('clients.fields.type'))->options(ClientType::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
            PowersOfAttorneyRelationManager::class,
            CasesRelationManager::class,
            CompaniesRelationManager::class,
            InvoicesRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
