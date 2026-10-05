<?php

namespace App\Filament\Admin\Resources\Companies;

use App\Enums\CompanyStatus;
use App\Enums\LegalForm;
use App\Filament\Admin\Resources\Companies\Pages\CreateCompany;
use App\Filament\Admin\Resources\Companies\Pages\EditCompany;
use App\Filament\Admin\Resources\Companies\Pages\ListCompanies;
use App\Filament\Admin\Resources\Companies\Pages\ViewCompany;
use App\Filament\Admin\Resources\Companies\RelationManagers\DeadlinesRelationManager;
use App\Filament\Admin\Resources\Companies\RelationManagers\DocumentsRelationManager;
use App\Filament\Admin\Resources\Companies\RelationManagers\PartnersRelationManager;
use App\Filament\Admin\Resources\Companies\RelationManagers\ProceduresRelationManager;
use App\Filament\Admin\Resources\Companies\RelationManagers\TasksRelationManager;
use App\Models\Company;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
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

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $slug = 'companies';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.companies');
    }

    public static function getModelLabel(): string
    {
        return __('companies.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('companies.plural');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'name_en', 'commercial_register_no', 'gafi_file_no', 'tax_card_no'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('companies.sections.basic'))->columns(3)->schema([
                TextInput::make('name')->label(__('companies.fields.name'))->required()->columnSpan(2),
                Select::make('status')->label(__('app.fields.status'))->options(CompanyStatus::class)->default(CompanyStatus::UNDER_INCORPORATION)->required(),
                TextInput::make('name_en')->label(__('companies.fields.name_en'))->columnSpan(2),
                Select::make('legal_form')->label(__('companies.fields.legal_form'))->options(LegalForm::class)->default(LegalForm::LLC)->required(),
                Select::make('client_id')
                    ->label(__('cases.fields.client'))
                    ->relationship('client', 'name')
                    ->searchable(['name', 'code'])
                    ->preload()
                    ->required()
                    ->default(fn () => request()->integer('client_id') ?: null),
                Select::make('responsible_lawyer_id')->label(__('companies.fields.responsible_lawyer'))->options(fn () => User::lawyers()->pluck('name', 'id'))->searchable(),
                TextInput::make('law')->label(__('companies.fields.law'))->datalist(['قانون 159 لسنة 1981', 'قانون 72 لسنة 2017 (الاستثمار)', 'قانون 17 لسنة 1999 (التجارة)', 'قانون 8 لسنة 1997']),
                Textarea::make('activity')->label(__('companies.fields.activity'))->rows(2)->columnSpanFull(),
            ]),
            Section::make(__('companies.sections.capital'))->columns(4)->schema([
                TextInput::make('authorized_capital')->label(__('companies.fields.authorized_capital'))->numeric(),
                TextInput::make('issued_capital')->label(__('companies.fields.issued_capital'))->numeric(),
                TextInput::make('paid_capital')->label(__('companies.fields.paid_capital'))->numeric(),
                TextInput::make('currency')->label(__('companies.fields.currency'))->default('EGP')->maxLength(5),
            ]),
            Section::make(__('companies.sections.registration'))->columns(3)->schema([
                TextInput::make('commercial_register_no')->label(__('companies.fields.commercial_register_no')),
                TextInput::make('commercial_register_office')->label(__('companies.fields.commercial_register_office')),
                DatePicker::make('commercial_register_expires_at')->label(__('companies.fields.commercial_register_expires_at')),
                TextInput::make('tax_card_no')->label(__('companies.fields.tax_card_no')),
                TextInput::make('tax_office')->label(__('companies.fields.tax_office')),
                Select::make('fiscal_year_end_month')->label(__('companies.fields.fiscal_year_end_month'))->options(__('app.months'))->default(12),
                TextInput::make('gafi_file_no')->label(__('companies.fields.gafi_file_no')),
                TextInput::make('gafi_license_no')->label(__('companies.fields.gafi_license_no')),
                DatePicker::make('incorporated_at')->label(__('companies.fields.incorporated_at')),
            ]),
            Section::make(__('companies.sections.address'))->columns(2)->schema([
                TextInput::make('address')->label(__('clients.fields.address')),
                TextInput::make('governorate')->label(__('companies.fields.governorate')),
                Textarea::make('notes')->label(__('app.fields.notes'))->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('companies.sections.basic'))->columns(4)->schema([
                TextEntry::make('name')->label(__('companies.fields.name'))->weight('bold')->columnSpan(2),
                TextEntry::make('legal_form')->label(__('companies.fields.legal_form'))->badge()->color('gray'),
                TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                TextEntry::make('client.name')->label(__('cases.fields.client')),
                TextEntry::make('responsibleLawyer.name')->label(__('companies.fields.responsible_lawyer'))->placeholder('-'),
                TextEntry::make('law')->label(__('companies.fields.law'))->placeholder('-'),
                TextEntry::make('incorporated_at')->label(__('companies.fields.incorporated_at'))->date()->placeholder('-'),
                TextEntry::make('activity')->label(__('companies.fields.activity'))->placeholder('-')->columnSpanFull(),
            ]),
            Section::make(__('companies.sections.registration'))->columns(4)->schema([
                TextEntry::make('commercial_register_no')->label(__('companies.fields.commercial_register_no'))->placeholder('-')->copyable(),
                TextEntry::make('commercial_register_office')->label(__('companies.fields.commercial_register_office'))->placeholder('-'),
                TextEntry::make('commercial_register_expires_at')->label(__('companies.fields.commercial_register_expires_at'))->date()->placeholder('-')
                    ->color(fn (Company $r) => $r->commercial_register_expires_at?->isBefore(now()->addMonths(2)) ? 'danger' : null),
                TextEntry::make('tax_card_no')->label(__('companies.fields.tax_card_no'))->placeholder('-'),
                TextEntry::make('gafi_file_no')->label(__('companies.fields.gafi_file_no'))->placeholder('-')->copyable(),
                TextEntry::make('gafi_license_no')->label(__('companies.fields.gafi_license_no'))->placeholder('-'),
                TextEntry::make('issued_capital')->label(__('companies.fields.issued_capital'))->money(fn (Company $r) => $r->currency)->placeholder('-'),
                TextEntry::make('paid_capital')->label(__('companies.fields.paid_capital'))->money(fn (Company $r) => $r->currency)->placeholder('-'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['client', 'responsibleLawyer'])->withCount(['procedures as open_procedures_count' => fn ($q) => $q->open()]))
            ->columns([
                TextColumn::make('name')->label(__('companies.fields.name'))->searchable()->sortable()->weight('bold')->description(fn (Company $r) => $r->name_en),
                TextColumn::make('legal_form')->label(__('companies.fields.legal_form'))->badge()->color('gray'),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable()->limit(25),
                TextColumn::make('commercial_register_no')->label(__('companies.fields.commercial_register_no'))->searchable(),
                TextColumn::make('gafi_file_no')->label(__('companies.fields.gafi_file_no'))->searchable()->toggleable(),
                TextColumn::make('commercial_register_expires_at')->label(__('companies.fields.commercial_register_expires_at'))->date()->sortable()
                    ->color(fn (Company $r) => $r->commercial_register_expires_at?->isBefore(now()->addMonths(2)) ? 'danger' : null),
                TextColumn::make('open_procedures_count')->label(__('companies.fields.open_procedures'))->alignCenter()->badge()->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('responsibleLawyer.name')->label(__('companies.fields.responsible_lawyer'))->toggleable(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(CompanyStatus::class)->multiple(),
                SelectFilter::make('legal_form')->label(__('companies.fields.legal_form'))->options(LegalForm::class),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
                TrashedFilter::make(),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PartnersRelationManager::class,
            ProceduresRelationManager::class,
            DeadlinesRelationManager::class,
            DocumentsRelationManager::class,
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view' => ViewCompany::route('/{record}'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
