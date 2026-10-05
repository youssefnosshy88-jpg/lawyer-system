<?php

namespace App\Filament\Portal\Resources\Cases;

use App\Enums\CaseStatus;
use App\Filament\Portal\Resources\Cases\Pages\ListCases;
use App\Filament\Portal\Resources\Cases\Pages\ViewCase;
use App\Filament\Portal\Resources\Cases\RelationManagers\ActivitiesRelationManager;
use App\Filament\Portal\Resources\Cases\RelationManagers\DocumentsRelationManager;
use App\Filament\Portal\Resources\Cases\RelationManagers\HearingsRelationManager;
use App\Models\LegalCase;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CaseResource extends Resource
{
    protected static ?string $model = LegalCase::class;

    protected static ?string $slug = 'cases';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('cases.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('cases.plural');
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
        return parent::getEloquentQuery()
            ->where('client_id', auth()->user()->client_id)
            ->where('visible_to_client', true)
            ->with(['court', 'caseType', 'leadLawyer', 'nextHearing']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextEntry::make('full_number')->label(__('cases.fields.case_number')),
                TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                TextEntry::make('title')->label(__('cases.fields.title'))->columnSpan(2),
                TextEntry::make('client_role')->label(__('cases.fields.client_role')),
                TextEntry::make('caseType.display_name')->label(__('cases.fields.case_type'))->placeholder('-'),
                TextEntry::make('court.display_name')->label(__('cases.fields.court'))->placeholder('-'),
                TextEntry::make('degree')->label(__('cases.fields.degree')),
                TextEntry::make('leadLawyer.name')->label(__('cases.fields.lead_lawyer'))->placeholder('-'),
                TextEntry::make('filed_at')->label(__('cases.fields.filed_at'))->date()->placeholder('-'),
                TextEntry::make('nextHearing.scheduled_at')->label(__('cases.fields.next_hearing'))->dateTime('Y-m-d H:i')->placeholder('-')->color('warning'),
                TextEntry::make('subject')->label(__('cases.fields.subject'))->placeholder('-')->columnSpanFull(),
                TextEntry::make('judgment_summary')->label(__('cases.fields.judgment_summary'))->placeholder('-')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextColumn::make('full_number')->label(__('cases.fields.case_number')),
                TextColumn::make('title')->label(__('cases.fields.title'))->searchable()->limit(40),
                TextColumn::make('court.display_name')->label(__('cases.fields.court')),
                TextColumn::make('leadLawyer.name')->label(__('cases.fields.lead_lawyer')),
                TextColumn::make('nextHearing.scheduled_at')->label(__('cases.fields.next_hearing'))->dateTime('Y-m-d')->color('warning'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(CaseStatus::class),
            ])
            ->recordActions([ViewAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [HearingsRelationManager::class, ActivitiesRelationManager::class, DocumentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCases::route('/'),
            'view' => ViewCase::route('/{record}'),
        ];
    }
}
