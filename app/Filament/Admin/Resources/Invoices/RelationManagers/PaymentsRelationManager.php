<?php

namespace App\Filament\Admin\Resources\Invoices\RelationManagers;

use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('billing.payments.plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_no')->label(__('billing.payments.receipt_no'))->weight('bold'),
                TextColumn::make('paid_at')->label(__('billing.payments.paid_at'))->date(),
                TextColumn::make('amount')->label(__('billing.fields.amount'))->money(fn () => $this->getOwnerRecord()->currency),
                TextColumn::make('method')->label(__('billing.payments.method'))->badge()->color('gray'),
                TextColumn::make('reference')->label(__('billing.payments.reference')),
                TextColumn::make('receiver.name')->label(__('billing.payments.received_by')),
            ])
            ->recordActions([DeleteAction::make()])
            ->defaultSort('paid_at', 'desc');
    }
}
