<?php

namespace App\Filament\Admin\Resources\CompanyProcedures;

use App\Enums\ProcedureStatus;
use App\Enums\ProcedureType;
use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Filament\Admin\Resources\CompanyProcedures\Pages\CreateCompanyProcedure;
use App\Filament\Admin\Resources\CompanyProcedures\Pages\EditCompanyProcedure;
use App\Filament\Admin\Resources\CompanyProcedures\Pages\ListCompanyProcedures;
use App\Filament\Admin\Resources\CompanyProcedures\Pages\ViewCompanyProcedure;
use App\Filament\Admin\Resources\CompanyProcedures\RelationManagers\DocumentsRelationManager;
use App\Filament\Admin\Resources\CompanyProcedures\RelationManagers\TasksRelationManager;
use App\Models\CompanyProcedure;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CompanyProcedureResource extends Resource
{
    protected static ?string $model = CompanyProcedure::class;

    protected static ?string $slug = 'company-procedures';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 51;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.companies');
    }

    public static function getModelLabel(): string
    {
        return __('procedures.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('procedures.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::open()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function defaultChecklist(string $type): array
    {
        $items = __("procedures.checklists.{$type}");

        if (! is_array($items)) {
            return [];
        }

        return array_map(fn (string $item) => ['item' => $item, 'done' => false], $items);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('procedures.sections.basic'))->columns(3)->schema([
                Select::make('company_id')
                    ->label(__('companies.singular'))
                    ->relationship('company', 'name')
                    ->searchable(['name', 'commercial_register_no'])
                    ->preload()
                    ->required()
                    ->default(fn () => request()->integer('company_id') ?: null),
                Select::make('type')
                    ->label(__('procedures.fields.type'))
                    ->options(ProcedureType::class)
                    ->default(ProcedureType::INCORPORATION)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, ?CompanyProcedure $record): void {
                        if ($record === null && $state) {
                            $set('checklist', static::defaultChecklist($state instanceof ProcedureType ? $state->value : $state));
                        }
                    }),
                Select::make('status')->label(__('app.fields.status'))->options(ProcedureStatus::class)->default(ProcedureStatus::DRAFT)->required(),
                TextInput::make('authority')->label(__('procedures.fields.authority'))->default('GAFI')->datalist(['GAFI', 'السجل التجاري', 'مصلحة الضرائب', 'التأمينات الاجتماعية', 'الهيئة العامة للرقابة المالية', 'الشهر العقاري']),
                TextInput::make('authority_reference')->label(__('procedures.fields.authority_reference')),
                Select::make('assigned_to')->label(__('procedures.fields.assigned_to'))->options(fn () => User::staff()->pluck('name', 'id'))->searchable()->default(auth()->id()),
                DatePicker::make('started_at')->label(__('procedures.fields.started_at'))->default(now()),
                DatePicker::make('submitted_at')->label(__('procedures.fields.submitted_at')),
                DatePicker::make('due_at')->label(__('procedures.fields.due_at')),
                DatePicker::make('completed_at')->label(__('procedures.fields.completed_at')),
                TextInput::make('government_fees')->label(__('procedures.fields.government_fees'))->numeric()->suffix(config('firm.currency')),
                TextInput::make('service_fees')->label(__('procedures.fields.service_fees'))->numeric()->suffix(config('firm.currency')),
                Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->default(true)->inline(false),
                Textarea::make('description')->label(__('cases.fields.description'))->rows(2)->columnSpanFull(),
            ]),
            Section::make(__('procedures.sections.checklist'))->schema([
                Repeater::make('checklist')
                    ->hiddenLabel()
                    ->default(fn () => static::defaultChecklist(ProcedureType::INCORPORATION->value))
                    ->columns(6)
                    ->addActionLabel(__('procedures.add_checklist_item'))
                    ->schema([
                        TextInput::make('item')->label(__('procedures.fields.checklist_item'))->required()->columnSpan(5),
                        Checkbox::make('done')->label(__('procedures.fields.done'))->inline(false),
                    ]),
            ]),
            Section::make(__('procedures.sections.result'))->collapsed()->schema([
                Textarea::make('result')->label(__('procedures.fields.result'))->rows(3),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(4)->schema([
                TextEntry::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextEntry::make('type')->label(__('procedures.fields.type'))->badge()->color('gray'),
                TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                TextEntry::make('checklist_progress')->label(__('procedures.fields.progress'))->suffix('%'),
                TextEntry::make('company.name')->label(__('companies.singular'))->url(fn (CompanyProcedure $r) => CompanyResource::getUrl('view', ['record' => $r->company_id])),
                TextEntry::make('authority')->label(__('procedures.fields.authority')),
                TextEntry::make('authority_reference')->label(__('procedures.fields.authority_reference'))->placeholder('-'),
                TextEntry::make('assignee.name')->label(__('procedures.fields.assigned_to'))->placeholder('-'),
                TextEntry::make('started_at')->label(__('procedures.fields.started_at'))->date()->placeholder('-'),
                TextEntry::make('submitted_at')->label(__('procedures.fields.submitted_at'))->date()->placeholder('-'),
                TextEntry::make('due_at')->label(__('procedures.fields.due_at'))->date()->placeholder('-'),
                TextEntry::make('completed_at')->label(__('procedures.fields.completed_at'))->date()->placeholder('-'),
                TextEntry::make('government_fees')->label(__('procedures.fields.government_fees'))->money(config('firm.currency'))->placeholder('-'),
                TextEntry::make('service_fees')->label(__('procedures.fields.service_fees'))->money(config('firm.currency'))->placeholder('-'),
                TextEntry::make('description')->label(__('cases.fields.description'))->placeholder('-')->columnSpan(2),
            ]),
            Section::make(__('procedures.sections.checklist'))->schema([
                RepeatableEntry::make('checklist')->hiddenLabel()->columns(6)->schema([
                    TextEntry::make('item')->hiddenLabel()->columnSpan(5),
                    IconEntry::make('done')->hiddenLabel()->boolean(),
                ]),
            ]),
            Section::make(__('procedures.sections.result'))->schema([
                TextEntry::make('result')->hiddenLabel()->placeholder('-'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['company.client', 'assignee']))
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->searchable()->weight('bold'),
                TextColumn::make('company.name')->label(__('companies.singular'))->searchable()->limit(30),
                TextColumn::make('type')->label(__('procedures.fields.type'))->badge()->color('gray'),
                TextColumn::make('authority')->label(__('procedures.fields.authority'))->toggleable(),
                TextColumn::make('assignee.name')->label(__('procedures.fields.assigned_to')),
                TextColumn::make('submitted_at')->label(__('procedures.fields.submitted_at'))->date()->sortable()->toggleable(),
                TextColumn::make('due_at')->label(__('procedures.fields.due_at'))->date()->sortable()
                    ->color(fn (CompanyProcedure $r) => $r->due_at?->isPast() && ! in_array($r->status, [ProcedureStatus::COMPLETED, ProcedureStatus::REJECTED], true) ? 'danger' : null),
                TextColumn::make('checklist_progress')->label(__('procedures.fields.progress'))->suffix('%')->alignCenter(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(ProcedureStatus::class)->multiple(),
                SelectFilter::make('type')->label(__('procedures.fields.type'))->options(ProcedureType::class),
                SelectFilter::make('assigned_to')->label(__('procedures.fields.assigned_to'))->relationship('assignee', 'name'),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [DocumentsRelationManager::class, TasksRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyProcedures::route('/'),
            'create' => CreateCompanyProcedure::route('/create'),
            'view' => ViewCompanyProcedure::route('/{record}'),
            'edit' => EditCompanyProcedure::route('/{record}/edit'),
        ];
    }
}
