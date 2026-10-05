<?php

namespace App\Filament\Admin\RelationManagers;

use App\Enums\DocumentCategory;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

abstract class BaseDocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('documents.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label(__('documents.fields.title'))->required()->maxLength(255),
            Select::make('category')->label(__('documents.fields.category'))->options(DocumentCategory::class)->default(DocumentCategory::OTHER)->required(),
            DatePicker::make('document_date')->label(__('documents.fields.document_date'))->default(now()),
            Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->inline(false),
            FileUpload::make('file_path')
                ->label(__('app.fields.file'))
                ->disk(config('firm.documents_disk'))
                ->directory(fn () => 'documents/'.now()->format('Y/m'))
                ->maxSize(20480)
                ->downloadable()
                ->openable()
                ->previewable()
                ->required()
                ->storeFileNamesIn('file_name')
                ->columnSpanFull(),
            Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('documents.fields.title'))->searchable()->weight('bold')->description(fn (Document $r) => $r->file_name),
                TextColumn::make('category')->label(__('documents.fields.category'))->badge()->color('gray'),
                TextColumn::make('document_date')->label(__('documents.fields.document_date'))->date(),
                TextColumn::make('size')->label(__('documents.fields.size'))->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 0).' KB' : '-'),
                IconColumn::make('visible_to_client')->label(__('app.fields.visible_to_client'))->boolean(),
                TextColumn::make('uploader.name')->label(__('documents.fields.uploaded_by'))->toggleable(),
                TextColumn::make('created_at')->label(__('app.fields.created_at'))->since()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->label(__('documents.fields.category'))->options(DocumentCategory::class),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                Action::make('download')
                    ->label(__('app.actions.download'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(fn (Document $record) => Storage::disk(config('firm.documents_disk'))->download($record->file_path, $record->file_name)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
