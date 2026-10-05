<?php

namespace App\Filament\Admin\Resources\Documents;

use App\Enums\DocumentCategory;
use App\Filament\Admin\Resources\Documents\Pages\CreateDocument;
use App\Filament\Admin\Resources\Documents\Pages\EditDocument;
use App\Filament\Admin\Resources\Documents\Pages\ListDocuments;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Document;
use App\Models\LegalCase;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $slug = 'documents';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?int $navigationSort = 30;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.documents');
    }

    public static function getModelLabel(): string
    {
        return __('documents.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('documents.plural');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'file_name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('title')->label(__('documents.fields.title'))->required()->maxLength(255),
                Select::make('category')->label(__('documents.fields.category'))->options(DocumentCategory::class)->default(DocumentCategory::OTHER)->required(),
                MorphToSelect::make('documentable')
                    ->label(__('documents.fields.attached_to'))
                    ->types([
                        MorphToSelect\Type::make(LegalCase::class)->label(__('cases.singular'))->titleAttribute('title'),
                        MorphToSelect\Type::make(Client::class)->label(__('clients.singular'))->titleAttribute('name'),
                        MorphToSelect\Type::make(Company::class)->label(__('companies.singular'))->titleAttribute('name'),
                        MorphToSelect\Type::make(Contract::class)->label(__('contracts.singular'))->titleAttribute('title'),
                    ])
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('document_date')->label(__('documents.fields.document_date'))->default(now()),
                Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->inline(false),
                FileUpload::make('file_path')
                    ->label(__('app.fields.file'))
                    ->disk(config('firm.documents_disk'))
                    ->directory(fn () => 'documents/'.now()->format('Y/m'))
                    ->maxSize(20480)
                    ->downloadable()
                    ->openable()
                    ->required()
                    ->storeFileNamesIn('file_name')
                    ->columnSpanFull(),
                Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['documentable', 'client', 'uploader']))
            ->columns([
                TextColumn::make('title')->label(__('documents.fields.title'))->searchable()->weight('bold')->description(fn (Document $r) => $r->file_name),
                TextColumn::make('category')->label(__('documents.fields.category'))->badge()->color('gray'),
                TextColumn::make('documentable_type')->label(__('documents.fields.attached_to'))
                    ->formatStateUsing(fn (Document $r) => static::describeOwner($r)),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->toggleable(),
                TextColumn::make('document_date')->label(__('documents.fields.document_date'))->date()->sortable(),
                IconColumn::make('visible_to_client')->label(__('app.fields.visible_to_client'))->boolean(),
                TextColumn::make('uploader.name')->label(__('documents.fields.uploaded_by'))->toggleable(),
                TextColumn::make('created_at')->label(__('app.fields.created_at'))->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->label(__('documents.fields.category'))->options(DocumentCategory::class)->multiple(),
                SelectFilter::make('documentable_type')->label(__('documents.fields.attached_to'))->options([
                    LegalCase::class => __('cases.singular'),
                    Client::class => __('clients.singular'),
                    Company::class => __('companies.singular'),
                    Contract::class => __('contracts.singular'),
                ]),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label(__('app.actions.download'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(fn (Document $record) => Storage::disk(config('firm.documents_disk'))->download($record->file_path, $record->file_name)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function describeOwner(Document $document): string
    {
        $owner = $document->documentable;

        return match (true) {
            $owner instanceof LegalCase => __('cases.singular').': '.$owner->full_number,
            $owner instanceof Client => __('clients.singular').': '.$owner->name,
            $owner instanceof Company => __('companies.singular').': '.$owner->name,
            $owner instanceof Contract => __('contracts.singular').': '.$owner->reference,
            default => class_basename($document->documentable_type),
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
