<?php

namespace App\Filament\Admin\Resources\Companies\RelationManagers;

use App\Filament\Admin\Resources\CompanyProcedures\CompanyProcedureResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProceduresRelationManager extends RelationManager
{
    protected static string $relationship = 'procedures';

    protected static ?string $relatedResource = CompanyProcedureResource::class;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('procedures.plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextColumn::make('type')->label(__('procedures.fields.type'))->badge()->color('gray'),
                TextColumn::make('authority')->label(__('procedures.fields.authority')),
                TextColumn::make('assignee.name')->label(__('procedures.fields.assigned_to')),
                TextColumn::make('due_at')->label(__('procedures.fields.due_at'))->date(),
                TextColumn::make('checklist_progress')->label(__('procedures.fields.progress'))->suffix('%')->alignCenter(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->headerActions([
                CreateAction::make()->url(fn () => CompanyProcedureResource::getUrl('create', ['company_id' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => CompanyProcedureResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
