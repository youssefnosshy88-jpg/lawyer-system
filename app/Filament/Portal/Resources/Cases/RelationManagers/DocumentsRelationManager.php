<?php

namespace App\Filament\Portal\Resources\Cases\RelationManagers;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('documents.plural');
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
                TextColumn::make('title')->label(__('documents.fields.title'))->weight('bold'),
                TextColumn::make('category')->label(__('documents.fields.category'))->badge()->color('gray'),
                TextColumn::make('document_date')->label(__('documents.fields.document_date'))->date(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label(__('app.actions.download'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(fn (Document $record) => Storage::disk(config('firm.documents_disk'))->download($record->file_path, $record->file_name)),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
