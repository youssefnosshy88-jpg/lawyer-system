<?php

namespace App\Filament\Portal\Resources\Companies\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeadlinesRelationManager extends RelationManager
{
    protected static string $relationship = 'deadlines';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('deadlines.plural');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('due_at')->label(__('deadlines.fields.due_at'))->date()->weight('bold'),
                TextColumn::make('type')->label(__('deadlines.fields.type'))->badge()->color('gray'),
                TextColumn::make('title')->label(__('deadlines.fields.title')),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->defaultSort('due_at');
    }
}
