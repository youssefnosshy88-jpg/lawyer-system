<?php

namespace App\Filament\Admin\Resources\Payments;

use App\Enums\PaymentMethod;
use App\Filament\Admin\Resources\Payments\Pages\CreatePayment;
use App\Filament\Admin\Resources\Payments\Pages\EditPayment;
use App\Filament\Admin\Resources\Payments\Pages\ListPayments;
use App\Models\Invoice;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $slug = 'payments';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 42;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.billing');
    }

    public static function getModelLabel(): string
    {
        return __('billing.payments.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('billing.payments.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(['name', 'code', 'phone'])->preload()->required()->live(),
                Select::make('invoice_id')
                    ->label(__('billing.invoices.singular'))
                    ->options(fn (Get $get) => Invoice::outstanding()
                        ->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id))
                        ->get()
                        ->mapWithKeys(fn (Invoice $i) => [$i->id => "{$i->number} - ".number_format($i->balance, 2)]))
                    ->searchable(),
                TextInput::make('amount')->label(__('billing.fields.amount'))->numeric()->required()->suffix(config('firm.currency')),
                Select::make('method')->label(__('billing.payments.method'))->options(PaymentMethod::class)->default(PaymentMethod::CASH)->required(),
                DatePicker::make('paid_at')->label(__('billing.payments.paid_at'))->default(now())->required(),
                TextInput::make('reference')->label(__('billing.payments.reference')),
                FileUpload::make('file_path')->label(__('billing.payments.attachment'))->disk(config('firm.documents_disk'))->directory('payments')->downloadable(),
                Textarea::make('notes')->label(__('app.fields.notes'))->rows(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['client', 'invoice', 'receiver']))
            ->columns([
                TextColumn::make('receipt_no')->label(__('billing.payments.receipt_no'))->searchable()->sortable()->weight('bold'),
                TextColumn::make('paid_at')->label(__('billing.payments.paid_at'))->date()->sortable(),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable(),
                TextColumn::make('invoice.number')->label(__('billing.invoices.singular'))->placeholder('-'),
                TextColumn::make('amount')->label(__('billing.fields.amount'))->money(config('firm.currency'))->sortable()->summarize(Sum::make()->money(config('firm.currency'))),
                TextColumn::make('method')->label(__('billing.payments.method'))->badge()->color('gray'),
                TextColumn::make('reference')->label(__('billing.payments.reference'))->toggleable(),
                TextColumn::make('receiver.name')->label(__('billing.payments.received_by'))->toggleable(),
            ])
            ->filters([
                SelectFilter::make('method')->label(__('billing.payments.method'))->options(PaymentMethod::class),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
                Filter::make('paid_at')
                    ->schema([
                        DatePicker::make('from')->label(__('app.filters.from')),
                        DatePicker::make('until')->label(__('app.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('paid_at', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('paid_at', '<=', $d))),
            ])
            ->recordActions([
                Action::make('receipt')
                    ->label(__('billing.payments.receipt'))
                    ->icon(Heroicon::OutlinedPrinter)
                    ->url(fn (Payment $record) => route('payments.receipt', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('paid_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
