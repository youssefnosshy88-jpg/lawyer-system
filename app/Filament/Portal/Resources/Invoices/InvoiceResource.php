<?php

namespace App\Filament\Portal\Resources\Invoices;

use App\Enums\InvoiceStatus;
use App\Filament\Portal\Resources\Invoices\Pages\ListInvoices;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'invoices';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('billing.invoices.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('billing.invoices.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('client_id', auth()->user()->client_id)
            ->whereNotIn('status', [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED])
            ->with('legalCase');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label(__('billing.invoices.number'))->weight('bold'),
                TextColumn::make('issued_at')->label(__('billing.invoices.issued_at'))->date()->sortable(),
                TextColumn::make('due_at')->label(__('billing.invoices.due_at'))->date(),
                TextColumn::make('legalCase.full_number')->label(__('cases.singular'))->placeholder('-'),
                TextColumn::make('total')->label(__('billing.invoices.total'))->money(fn (Invoice $r) => $r->currency),
                TextColumn::make('paid_amount')->label(__('billing.invoices.paid_amount'))->money(fn (Invoice $r) => $r->currency),
                TextColumn::make('balance')->label(__('billing.invoices.balance'))->money(fn (Invoice $r) => $r->currency)->color('danger'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->recordActions([
                Action::make('print')
                    ->label(__('app.actions.print'))
                    ->icon(Heroicon::OutlinedPrinter)
                    ->url(fn (Invoice $r) => route('invoices.print', $r))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListInvoices::route('/')];
    }
}
