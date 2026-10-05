<?php

namespace App\Filament\Admin\Resources\LegalCases;

use App\Enums\CaseDegree;
use App\Enums\CaseStatus;
use App\Enums\ClientRole;
use App\Filament\Admin\Resources\Clients\ClientResource;
use App\Filament\Admin\Resources\LegalCases\Pages\CreateLegalCase;
use App\Filament\Admin\Resources\LegalCases\Pages\EditLegalCase;
use App\Filament\Admin\Resources\LegalCases\Pages\ListLegalCases;
use App\Filament\Admin\Resources\LegalCases\Pages\ViewLegalCase;
use App\Filament\Admin\Resources\LegalCases\RelationManagers\ActivitiesRelationManager;
use App\Filament\Admin\Resources\LegalCases\RelationManagers\DocumentsRelationManager;
use App\Filament\Admin\Resources\LegalCases\RelationManagers\ExpensesRelationManager;
use App\Filament\Admin\Resources\LegalCases\RelationManagers\HearingsRelationManager;
use App\Filament\Admin\Resources\LegalCases\RelationManagers\OpponentsRelationManager;
use App\Filament\Admin\Resources\LegalCases\RelationManagers\TasksRelationManager;
use App\Models\CaseType;
use App\Models\Court;
use App\Models\LegalCase;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LegalCaseResource extends Resource
{
    protected static ?string $model = LegalCase::class;

    protected static ?string $slug = 'cases';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.cases');
    }

    public static function getModelLabel(): string
    {
        return __('cases.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('cases.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::active()->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'case_number', 'title', 'client.name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('cases.sections.basic'))
                ->columns(3)
                ->schema([
                    TextInput::make('title')->label(__('cases.fields.title'))->required()->maxLength(255)->columnSpan(2),
                    Select::make('status')->label(__('app.fields.status'))->options(CaseStatus::class)->default(CaseStatus::OPEN)->required(),
                    Select::make('client_id')
                        ->label(__('cases.fields.client'))
                        ->relationship('client', 'name')
                        ->searchable(['name', 'code', 'phone'])
                        ->preload()
                        ->required()
                        ->default(fn () => request()->integer('client_id') ?: null),
                    Select::make('client_role')->label(__('cases.fields.client_role'))->options(ClientRole::class)->default(ClientRole::PLAINTIFF)->required(),
                    Select::make('case_type_id')
                        ->label(__('cases.fields.case_type'))
                        ->options(fn () => CaseType::where('is_active', true)->get()->pluck('display_name', 'id'))
                        ->searchable(),
                    TextInput::make('case_number')->label(__('cases.fields.case_number'))->maxLength(60),
                    TextInput::make('case_year')->label(__('cases.fields.case_year'))->numeric()->default(now()->year)->minValue(1990)->maxValue(2100),
                    Select::make('degree')->label(__('cases.fields.degree'))->options(CaseDegree::class)->default(CaseDegree::FIRST_INSTANCE)->required(),
                    Select::make('court_id')
                        ->label(__('cases.fields.court'))
                        ->options(fn () => Court::where('is_active', true)->get()->pluck('display_name', 'id'))
                        ->searchable(),
                    TextInput::make('circuit')->label(__('cases.fields.circuit')),
                    TextInput::make('claim_amount')->label(__('cases.fields.claim_amount'))->numeric()->suffix(config('firm.currency')),
                ]),
            Section::make(__('cases.sections.team'))
                ->columns(3)
                ->schema([
                    Select::make('lead_lawyer_id')
                        ->label(__('cases.fields.lead_lawyer'))
                        ->options(fn () => User::lawyers()->pluck('name', 'id'))
                        ->searchable()
                        ->default(fn () => auth()->user()?->isLawyer() ? auth()->id() : null),
                    Select::make('lawyers')
                        ->label(__('cases.fields.lawyers'))
                        ->relationship('lawyers', 'name', fn (Builder $query) => $query->lawyers())
                        ->multiple()
                        ->preload(),
                    DatePicker::make('filed_at')->label(__('cases.fields.filed_at'))->default(now()),
                    Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->default(true)->inline(false),
                ]),
            Section::make(__('cases.sections.details'))
                ->columns(1)
                ->collapsible()
                ->schema([
                    Textarea::make('subject')->label(__('cases.fields.subject'))->rows(2),
                    Textarea::make('description')->label(__('cases.fields.description'))->rows(4),
                ]),
            Section::make(__('cases.sections.judgment'))
                ->columns(2)
                ->collapsed()
                ->schema([
                    DatePicker::make('closed_at')->label(__('cases.fields.closed_at')),
                    Textarea::make('judgment_summary')->label(__('cases.fields.judgment_summary'))->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('cases.sections.basic'))
                ->columns(4)
                ->schema([
                    TextEntry::make('reference')->label(__('cases.fields.reference'))->weight('bold')->copyable(),
                    TextEntry::make('full_number')->label(__('cases.fields.case_number')),
                    TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                    TextEntry::make('degree')->label(__('cases.fields.degree'))->badge()->color('gray'),
                    TextEntry::make('client.name')->label(__('cases.fields.client'))->url(fn (LegalCase $r) => $r->client_id ? ClientResource::getUrl('view', ['record' => $r->client_id]) : null),
                    TextEntry::make('client_role')->label(__('cases.fields.client_role')),
                    TextEntry::make('caseType.display_name')->label(__('cases.fields.case_type')),
                    TextEntry::make('court.display_name')->label(__('cases.fields.court')),
                    TextEntry::make('circuit')->label(__('cases.fields.circuit'))->placeholder('-'),
                    TextEntry::make('leadLawyer.name')->label(__('cases.fields.lead_lawyer'))->placeholder('-'),
                    TextEntry::make('lawyers.name')->label(__('cases.fields.lawyers'))->badge()->color('gray')->placeholder('-'),
                    TextEntry::make('claim_amount')->label(__('cases.fields.claim_amount'))->money(config('firm.currency'))->placeholder('-'),
                    TextEntry::make('filed_at')->label(__('cases.fields.filed_at'))->date()->placeholder('-'),
                    TextEntry::make('nextHearing.scheduled_at')->label(__('cases.fields.next_hearing'))->dateTime('Y-m-d H:i')->placeholder('-')->color('warning'),
                    TextEntry::make('opponents.name')->label(__('cases.fields.opponents'))->badge()->color('danger')->placeholder('-')->columnSpan(2),
                ]),
            Grid::make(2)->schema([
                Section::make(__('cases.sections.details'))->schema([
                    TextEntry::make('subject')->label(__('cases.fields.subject'))->placeholder('-'),
                    TextEntry::make('description')->label(__('cases.fields.description'))->placeholder('-')->markdown(),
                ]),
                Section::make(__('cases.sections.judgment'))->schema([
                    TextEntry::make('closed_at')->label(__('cases.fields.closed_at'))->date()->placeholder('-'),
                    TextEntry::make('judgment_summary')->label(__('cases.fields.judgment_summary'))->placeholder('-'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['client', 'court', 'caseType', 'leadLawyer', 'nextHearing']))
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->searchable()->sortable()->weight('bold'),
                TextColumn::make('full_number')->label(__('cases.fields.case_number'))->searchable(['case_number']),
                TextColumn::make('title')->label(__('cases.fields.title'))->searchable()->limit(35)->tooltip(fn (LegalCase $r) => $r->title),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable()->limit(25),
                TextColumn::make('caseType.display_name')->label(__('cases.fields.case_type'))->badge()->color(fn (LegalCase $r) => $r->caseType?->color ?? 'gray')->toggleable(),
                TextColumn::make('court.display_name')->label(__('cases.fields.court'))->limit(25)->toggleable(),
                TextColumn::make('leadLawyer.name')->label(__('cases.fields.lead_lawyer'))->toggleable(),
                TextColumn::make('nextHearing.scheduled_at')->label(__('cases.fields.next_hearing'))->dateTime('Y-m-d')->sortable()->color('warning'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge()->sortable(),
                TextColumn::make('filed_at')->label(__('cases.fields.filed_at'))->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(CaseStatus::class)->multiple(),
                SelectFilter::make('case_type_id')->label(__('cases.fields.case_type'))->relationship('caseType', 'name'),
                SelectFilter::make('court_id')->label(__('cases.fields.court'))->relationship('court', 'name')->searchable(),
                SelectFilter::make('lead_lawyer_id')->label(__('cases.fields.lead_lawyer'))->relationship('leadLawyer', 'name'),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
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
            HearingsRelationManager::class,
            ActivitiesRelationManager::class,
            OpponentsRelationManager::class,
            DocumentsRelationManager::class,
            ExpensesRelationManager::class,
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalCases::route('/'),
            'create' => CreateLegalCase::route('/create'),
            'view' => ViewLegalCase::route('/{record}'),
            'edit' => EditLegalCase::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
