<?php

namespace App\Filament\Portal\Resources\Documents;

use App\Filament\Portal\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $slug = 'documents';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('documents.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('documents.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('client_id', auth()->user()->client_id)
            ->where('visible_to_client', true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('documents.fields.title'))->weight('bold')->searchable(),
                TextColumn::make('category')->label(__('documents.fields.category'))->badge()->color('gray'),
                TextColumn::make('document_date')->label(__('documents.fields.document_date'))->date()->sortable(),
                TextColumn::make('created_at')->label(__('app.fields.created_at'))->since(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label(__('app.actions.download'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(fn (Document $record) => Storage::disk(config('firm.documents_disk'))->download($record->file_path, $record->file_name)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListDocuments::route('/')];
    }
}
