<?php

namespace App\Filament\Portal\Resources\Companies;

use App\Filament\Portal\Resources\Companies\Pages\ListCompanies;
use App\Filament\Portal\Resources\Companies\Pages\ViewCompany;
use App\Filament\Portal\Resources\Companies\RelationManagers\DeadlinesRelationManager;
use App\Filament\Portal\Resources\Companies\RelationManagers\ProceduresRelationManager;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $slug = 'companies';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('companies.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('companies.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return true;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', auth()->user()->client_id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('name')->label(__('companies.fields.name'))->weight('bold')->columnSpan(2),
                TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                TextEntry::make('legal_form')->label(__('companies.fields.legal_form')),
                TextEntry::make('commercial_register_no')->label(__('companies.fields.commercial_register_no'))->placeholder('-'),
                TextEntry::make('commercial_register_expires_at')->label(__('companies.fields.commercial_register_expires_at'))->date()->placeholder('-'),
                TextEntry::make('tax_card_no')->label(__('companies.fields.tax_card_no'))->placeholder('-'),
                TextEntry::make('gafi_file_no')->label(__('companies.fields.gafi_file_no'))->placeholder('-'),
                TextEntry::make('incorporated_at')->label(__('companies.fields.incorporated_at'))->date()->placeholder('-'),
                TextEntry::make('responsibleLawyer.name')->label(__('companies.fields.responsible_lawyer'))->placeholder('-'),
                TextEntry::make('activity')->label(__('companies.fields.activity'))->placeholder('-')->columnSpan(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('companies.fields.name'))->weight('bold'),
                TextColumn::make('legal_form')->label(__('companies.fields.legal_form'))->badge()->color('gray'),
                TextColumn::make('commercial_register_no')->label(__('companies.fields.commercial_register_no')),
                TextColumn::make('commercial_register_expires_at')->label(__('companies.fields.commercial_register_expires_at'))->date(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [ProceduresRelationManager::class, DeadlinesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'view' => ViewCompany::route('/{record}'),
        ];
    }
}
