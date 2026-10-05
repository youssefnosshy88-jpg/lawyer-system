<?php

namespace App\Filament\Admin\Resources\LegalCases\RelationManagers;

use App\Enums\ExpenseCategory;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('billing.expenses.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category')->label(__('billing.expenses.category'))->options(ExpenseCategory::class)->default(ExpenseCategory::COURT_FEES)->required(),
            TextInput::make('description')->label(__('billing.expenses.description'))->required(),
            TextInput::make('amount')->label(__('billing.fields.amount'))->numeric()->required()->suffix(config('firm.currency')),
            DatePicker::make('spent_at')->label(__('billing.expenses.spent_at'))->default(now())->required(),
            Toggle::make('billable')->label(__('billing.expenses.billable'))->default(true)->inline(false),
            FileUpload::make('receipt_path')->label(__('billing.expenses.receipt'))->disk(config('firm.documents_disk'))->directory('receipts')->downloadable(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('spent_at')->label(__('billing.expenses.spent_at'))->date()->sortable(),
                TextColumn::make('category')->label(__('billing.expenses.category'))->badge()->color('gray'),
                TextColumn::make('description')->label(__('billing.expenses.description'))->limit(40),
                TextColumn::make('amount')->label(__('billing.fields.amount'))->money(config('firm.currency'))->summarize(Sum::make()->money(config('firm.currency'))),
                IconColumn::make('billable')->label(__('billing.expenses.billable'))->boolean(),
                IconColumn::make('invoice_id')->label(__('billing.expenses.invoiced'))->boolean()->getStateUsing(fn ($record) => $record->invoice_id !== null),
                TextColumn::make('payer.name')->label(__('billing.expenses.paid_by'))->toggleable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('spent_at', 'desc');
    }
}
