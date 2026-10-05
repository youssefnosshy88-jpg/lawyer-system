<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label(__('billing.invoices.actions.mark_sent'))
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->visible(fn (Invoice $record) => $record->status === InvoiceStatus::DRAFT)
                ->requiresConfirmation()
                ->action(function (Invoice $record): void {
                    $record->status = InvoiceStatus::SENT;
                    $record->save();
                    $record->recalculateTotals();
                }),
            InvoiceResource::recordPaymentAction(),
            Action::make('print')
                ->label(__('app.actions.print'))
                ->icon(Heroicon::OutlinedPrinter)
                ->url(fn (Invoice $record) => route('invoices.print', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
