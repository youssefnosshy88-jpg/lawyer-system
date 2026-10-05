<?php

namespace App\Filament\Admin\Resources\Expenses;

use App\Enums\ExpenseCategory;
use App\Filament\Admin\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Admin\Resources\Expenses\Pages\EditExpense;
use App\Filament\Admin\Resources\Expenses\Pages\ListExpenses;
use App\Models\Expense;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?int $navigationSort = 43;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.billing');
    }

    public static function getModelLabel(): string
    {
        return __('billing.expenses.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('billing.expenses.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable()->preload()->live(),
                Select::make('legal_case_id')->label(__('cases.singular'))
                    ->relationship('legalCase', 'title', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()->preload(),
                Select::make('company_id')->label(__('companies.singular'))
                    ->relationship('company', 'name', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()->preload(),
                Select::make('category')->label(__('billing.expenses.category'))->options(ExpenseCategory::class)->default(ExpenseCategory::COURT_FEES)->required(),
                TextInput::make('description')->label(__('billing.expenses.description'))->required()->columnSpanFull(),
                TextInput::make('amount')->label(__('billing.fields.amount'))->numeric()->required()->suffix(config('firm.currency')),
                DatePicker::make('spent_at')->label(__('billing.expenses.spent_at'))->default(now())->required(),
                Toggle::make('billable')->label(__('billing.expenses.billable'))->default(true)->inline(false),
                FileUpload::make('receipt_path')->label(__('billing.expenses.receipt'))->disk(config('firm.documents_disk'))->directory('receipts')->downloadable(),
                Textarea::make('notes')->label(__('app.fields.notes'))->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['client', 'legalCase', 'company', 'payer']))
            ->columns([
                TextColumn::make('spent_at')->label(__('billing.expenses.spent_at'))->date()->sortable(),
                TextColumn::make('category')->label(__('billing.expenses.category'))->badge()->color('gray'),
                TextColumn::make('description')->label(__('billing.expenses.description'))->searchable()->limit(35),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable(),
                TextColumn::make('legalCase.full_number')->label(__('cases.singular'))->placeholder('-')->toggleable(),
                TextColumn::make('company.name')->label(__('companies.singular'))->placeholder('-')->toggleable(),
                TextColumn::make('amount')->label(__('billing.fields.amount'))->money(config('firm.currency'))->sortable()->summarize(Sum::make()->money(config('firm.currency'))),
                IconColumn::make('billable')->label(__('billing.expenses.billable'))->boolean(),
                IconColumn::make('invoice_id')->label(__('billing.expenses.invoiced'))->boolean()->getStateUsing(fn (Expense $r) => $r->invoice_id !== null),
                TextColumn::make('payer.name')->label(__('billing.expenses.paid_by'))->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->label(__('billing.expenses.category'))->options(ExpenseCategory::class),
                TernaryFilter::make('billable')->label(__('billing.expenses.billable')),
                TernaryFilter::make('invoiced')->label(__('billing.expenses.invoiced'))->nullable()
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('invoice_id'),
                        false: fn (Builder $q) => $q->whereNull('invoice_id'),
                    ),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('spent_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
            'create' => CreateExpense::route('/create'),
            'edit' => EditExpense::route('/{record}/edit'),
        ];
    }
}
