<?php

namespace App\Filament\Admin\Resources\Contracts;

use App\Enums\ContractStatus;
use App\Filament\Admin\Resources\Contracts\Pages\CreateContract;
use App\Filament\Admin\Resources\Contracts\Pages\EditContract;
use App\Filament\Admin\Resources\Contracts\Pages\ListContracts;
use App\Filament\Admin\Resources\Contracts\Pages\ViewContract;
use App\Filament\Admin\Resources\Contracts\RelationManagers\DocumentsRelationManager;
use App\Models\Contract;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $slug = 'contracts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.documents');
    }

    public static function getModelLabel(): string
    {
        return __('contracts.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('contracts.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextInput::make('title')->label(__('contracts.fields.title'))->required()->columnSpan(2),
                Select::make('status')->label(__('app.fields.status'))->options(ContractStatus::class)->default(ContractStatus::DRAFT)->required(),
                Select::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable()->preload()->required()->live(),
                Select::make('legal_case_id')->label(__('cases.singular'))
                    ->relationship('legalCase', 'title', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()->preload(),
                Select::make('company_id')->label(__('companies.singular'))
                    ->relationship('company', 'name', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()->preload(),
                TextInput::make('type')->label(__('contracts.fields.type'))->datalist(__('contracts.type_suggestions')),
                TextInput::make('second_party')->label(__('contracts.fields.second_party')),
                TextInput::make('value')->label(__('contracts.fields.value'))->numeric()->suffix(config('firm.currency')),
                DatePicker::make('signed_at')->label(__('contracts.fields.signed_at')),
                DatePicker::make('starts_at')->label(__('contracts.fields.starts_at')),
                DatePicker::make('ends_at')->label(__('contracts.fields.ends_at')),
                Select::make('drafted_by')->label(__('contracts.fields.drafted_by'))->options(fn () => User::staff()->pluck('name', 'id'))->default(auth()->id()),
                Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->default(true)->inline(false),
                FileUpload::make('file_path')->label(__('app.fields.file'))->disk(config('firm.documents_disk'))->directory('contracts')->downloadable()->openable()->columnSpanFull(),
                Textarea::make('summary')->label(__('contracts.fields.summary'))->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextEntry::make('title')->label(__('contracts.fields.title'))->columnSpan(2),
                TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                TextEntry::make('client.name')->label(__('cases.fields.client')),
                TextEntry::make('second_party')->label(__('contracts.fields.second_party'))->placeholder('-'),
                TextEntry::make('type')->label(__('contracts.fields.type'))->placeholder('-'),
                TextEntry::make('value')->label(__('contracts.fields.value'))->money(config('firm.currency'))->placeholder('-'),
                TextEntry::make('signed_at')->label(__('contracts.fields.signed_at'))->date()->placeholder('-'),
                TextEntry::make('starts_at')->label(__('contracts.fields.starts_at'))->date()->placeholder('-'),
                TextEntry::make('ends_at')->label(__('contracts.fields.ends_at'))->date()->placeholder('-'),
                TextEntry::make('drafter.name')->label(__('contracts.fields.drafted_by'))->placeholder('-'),
                TextEntry::make('summary')->label(__('contracts.fields.summary'))->placeholder('-')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['client', 'drafter']))
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->searchable()->weight('bold'),
                TextColumn::make('title')->label(__('contracts.fields.title'))->searchable()->limit(35),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable(),
                TextColumn::make('second_party')->label(__('contracts.fields.second_party'))->toggleable(),
                TextColumn::make('value')->label(__('contracts.fields.value'))->money(config('firm.currency'))->sortable(),
                TextColumn::make('ends_at')->label(__('contracts.fields.ends_at'))->date()->sortable()
                    ->color(fn (Contract $r) => $r->ends_at?->isBefore(now()->addMonth()) ? 'danger' : null),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(ContractStatus::class),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [DocumentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'view' => ViewContract::route('/{record}'),
            'edit' => EditContract::route('/{record}/edit'),
        ];
    }
}
