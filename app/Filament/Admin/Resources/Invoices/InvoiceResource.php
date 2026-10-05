<?php

namespace App\Filament\Admin\Resources\Invoices;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Filament\Admin\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Admin\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Admin\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Admin\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Admin\Resources\Invoices\RelationManagers\PaymentsRelationManager;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'invoices';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?int $navigationSort = 41;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.billing');
    }

    public static function getModelLabel(): string
    {
        return __('billing.invoices.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('billing.invoices.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', InvoiceStatus::OVERDUE)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('billing.invoices.sections.header'))->columns(3)->schema([
                Select::make('client_id')
                    ->label(__('cases.fields.client'))
                    ->relationship('client', 'name')
                    ->searchable(['name', 'code', 'phone'])
                    ->preload()
                    ->required()
                    ->live()
                    ->default(fn () => request()->integer('client_id') ?: null),
                Select::make('legal_case_id')
                    ->label(__('cases.singular'))
                    ->relationship('legalCase', 'title', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()
                    ->preload(),
                Select::make('company_id')
                    ->label(__('companies.singular'))
                    ->relationship('company', 'name', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()
                    ->preload(),
                Select::make('fee_agreement_id')
                    ->label(__('billing.fee_agreements.singular'))
                    ->relationship('feeAgreement', 'reference', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()
                    ->preload(),
                Select::make('status')->label(__('app.fields.status'))->options(InvoiceStatus::class)->default(InvoiceStatus::DRAFT)->required(),
                DatePicker::make('issued_at')->label(__('billing.invoices.issued_at'))->default(now())->required(),
                DatePicker::make('due_at')->label(__('billing.invoices.due_at'))->default(now()->addDays(30)),
                TextInput::make('discount')->label(__('billing.invoices.discount'))->numeric()->default(0)->suffix(config('firm.currency')),
                TextInput::make('tax_rate')->label(__('billing.invoices.tax_rate'))->numeric()->default(0)->suffix('%'),
            ]),
            Section::make(__('billing.invoices.sections.items'))->schema([
                Repeater::make('items')
                    ->relationship()
                    ->hiddenLabel()
                    ->columns(6)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->addActionLabel(__('billing.invoices.add_item'))
                    ->schema([
                        TextInput::make('description')->label(__('billing.invoices.item_description'))->required()->columnSpan(3),
                        TextInput::make('quantity')->label(__('billing.invoices.quantity'))->numeric()->default(1)->required()->live(onBlur: true),
                        TextInput::make('unit_price')->label(__('billing.invoices.unit_price'))->numeric()->required()->default(0)->live(onBlur: true),
                        TextEntry::make('line_total')->label(__('billing.invoices.total'))
                            ->state(fn (Get $get) => number_format((float) $get('quantity') * (float) $get('unit_price'), 2)),
                    ]),
            ]),
            Section::make(__('app.sections.notes'))->collapsed()->schema([
                Textarea::make('notes')->label(__('app.fields.notes'))->rows(3),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(4)->schema([
                TextEntry::make('number')->label(__('billing.invoices.number'))->weight('bold')->size('lg'),
                TextEntry::make('status')->label(__('app.fields.status'))->badge(),
                TextEntry::make('issued_at')->label(__('billing.invoices.issued_at'))->date(),
                TextEntry::make('due_at')->label(__('billing.invoices.due_at'))->date()->placeholder('-'),
                TextEntry::make('client.name')->label(__('cases.fields.client')),
                TextEntry::make('legalCase.display_title')->label(__('cases.singular'))->placeholder('-'),
                TextEntry::make('company.name')->label(__('companies.singular'))->placeholder('-'),
                TextEntry::make('feeAgreement.reference')->label(__('billing.fee_agreements.singular'))->placeholder('-'),
            ]),
            Section::make(__('billing.invoices.sections.items'))->schema([
                RepeatableEntry::make('items')->hiddenLabel()->columns(4)->schema([
                    TextEntry::make('description')->label(__('billing.invoices.item_description'))->columnSpan(2),
                    TextEntry::make('quantity')->label(__('billing.invoices.quantity')),
                    TextEntry::make('total')->label(__('billing.invoices.total'))->money(fn (Invoice $r) => $r->currency),
                ]),
            ]),
            Grid::make(5)->schema([
                TextEntry::make('subtotal')->label(__('billing.invoices.subtotal'))->money(fn (Invoice $r) => $r->currency),
                TextEntry::make('discount')->label(__('billing.invoices.discount'))->money(fn (Invoice $r) => $r->currency),
                TextEntry::make('tax_amount')->label(__('billing.invoices.tax_amount'))->money(fn (Invoice $r) => $r->currency),
                TextEntry::make('total')->label(__('billing.invoices.total'))->money(fn (Invoice $r) => $r->currency)->weight('bold'),
                TextEntry::make('balance')->label(__('billing.invoices.balance'))->money(fn (Invoice $r) => $r->currency)->weight('bold')->color(fn (Invoice $r) => $r->balance > 0 ? 'danger' : 'success'),
            ]),
            TextEntry::make('notes')->label(__('app.fields.notes'))->placeholder('-')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['client', 'legalCase']))
            ->columns([
                TextColumn::make('number')->label(__('billing.invoices.number'))->searchable()->sortable()->weight('bold'),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable()->limit(25),
                TextColumn::make('legalCase.full_number')->label(__('cases.singular'))->toggleable(),
                TextColumn::make('issued_at')->label(__('billing.invoices.issued_at'))->date()->sortable(),
                TextColumn::make('due_at')->label(__('billing.invoices.due_at'))->date()->sortable()
                    ->color(fn (Invoice $r) => $r->status === InvoiceStatus::OVERDUE ? 'danger' : null),
                TextColumn::make('total')->label(__('billing.invoices.total'))->money(fn (Invoice $r) => $r->currency)->sortable()->summarize(Sum::make()->money(config('firm.currency'))),
                TextColumn::make('paid_amount')->label(__('billing.invoices.paid_amount'))->money(fn (Invoice $r) => $r->currency)->summarize(Sum::make()->money(config('firm.currency'))),
                TextColumn::make('balance')->label(__('billing.invoices.balance'))->money(fn (Invoice $r) => $r->currency)->color('danger'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(InvoiceStatus::class)->multiple(),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
            ])
            ->recordActions([
                static::recordPaymentAction(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function recordPaymentAction(): Action
    {
        return Action::make('record_payment')
            ->label(__('billing.payments.record'))
            ->icon(Heroicon::OutlinedBanknotes)
            ->color('success')
            ->visible(fn (Invoice $record) => $record->balance > 0 && ! in_array($record->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED], true) && auth()->user()->can('create_payments'))
            ->schema([
                TextInput::make('amount')->label(__('billing.fields.amount'))->numeric()->required()->default(fn (Invoice $record) => $record->balance)->maxValue(fn (Invoice $record) => $record->balance)->suffix(fn (Invoice $record) => $record->currency),
                Select::make('method')->label(__('billing.payments.method'))->options(PaymentMethod::class)->default(PaymentMethod::CASH)->required(),
                DatePicker::make('paid_at')->label(__('billing.payments.paid_at'))->default(now())->required(),
                TextInput::make('reference')->label(__('billing.payments.reference')),
                Textarea::make('notes')->label(__('app.fields.notes'))->rows(2),
            ])
            ->action(function (Invoice $record, array $data): void {
                $record->payments()->create($data + ['client_id' => $record->client_id, 'received_by' => auth()->id()]);

                Notification::make()->success()->title(__('billing.payments.recorded'))->send();
            });
    }

    public static function getRelations(): array
    {
        return [PaymentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view' => ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
