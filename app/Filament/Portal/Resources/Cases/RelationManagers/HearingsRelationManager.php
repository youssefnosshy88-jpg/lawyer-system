<?php

namespace App\Filament\Portal\Resources\Cases\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HearingsRelationManager extends RelationManager
{
    protected static string $relationship = 'hearings';

    protected static bool $isLazy = false;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('hearings.plural');
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
                TextColumn::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->dateTime('Y-m-d H:i')->weight('bold'),
                TextColumn::make('type')->label(__('hearings.fields.type'))->badge()->color('gray'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
                TextColumn::make('outcome')->label(__('hearings.fields.outcome'))->wrap(),
                TextColumn::make('decision')->label(__('hearings.fields.decision'))->wrap(),
                TextColumn::make('next_hearing_at')->label(__('hearings.fields.next_hearing_at'))->date(),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }
}
