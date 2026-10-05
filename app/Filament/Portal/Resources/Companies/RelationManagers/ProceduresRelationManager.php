<?php

namespace App\Filament\Portal\Resources\Companies\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProceduresRelationManager extends RelationManager
{
    protected static string $relationship = 'procedures';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('procedures.plural');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->where('visible_to_client', true))
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextColumn::make('type')->label(__('procedures.fields.type'))->badge()->color('gray'),
                TextColumn::make('authority')->label(__('procedures.fields.authority')),
                TextColumn::make('submitted_at')->label(__('procedures.fields.submitted_at'))->date(),
                TextColumn::make('checklist_progress')->label(__('procedures.fields.progress'))->suffix('%'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
