<?php

namespace App\Filament\Portal\Resources\Cases\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('cases.activities.title');
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
                TextColumn::make('occurred_at')->label(__('cases.activities.occurred_at'))->dateTime('Y-m-d'),
                TextColumn::make('type')->label(__('cases.activities.type'))->badge()->color('gray'),
                TextColumn::make('title')->label(__('cases.activities.title_field'))->weight('bold')->description(fn ($record) => $record->body),
            ])
            ->defaultSort('occurred_at', 'desc');
    }
}
