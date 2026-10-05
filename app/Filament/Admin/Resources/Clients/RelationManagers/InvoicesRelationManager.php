<?php

namespace App\Filament\Admin\Resources\Clients\RelationManagers;

use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $relatedResource = InvoiceResource::class;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('billing.invoices.plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label(__('billing.invoices.number'))->weight('bold'),
                TextColumn::make('issued_at')->label(__('billing.invoices.issued_at'))->date(),
                TextColumn::make('due_at')->label(__('billing.invoices.due_at'))->date(),
                TextColumn::make('total')->label(__('billing.invoices.total'))->money(fn ($record) => $record->currency),
                TextColumn::make('paid_amount')->label(__('billing.invoices.paid_amount'))->money(fn ($record) => $record->currency),
                TextColumn::make('balance')->label(__('billing.invoices.balance'))->money(fn ($record) => $record->currency)->color('danger'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->headerActions([
                CreateAction::make()->url(fn () => InvoiceResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => InvoiceResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
