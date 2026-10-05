<?php

namespace App\Filament\Admin\Resources\Clients\RelationManagers;

use App\Enums\PoaStatus;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PowersOfAttorneyRelationManager extends RelationManager
{
    protected static string $relationship = 'powersOfAttorney';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('clients.poa.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('number')->label(__('clients.poa.number'))->required(),
            TextInput::make('notary_office')->label(__('clients.poa.notary_office')),
            DatePicker::make('issued_at')->label(__('clients.poa.issued_at'))->required()->default(now()),
            DatePicker::make('expires_at')->label(__('clients.poa.expires_at')),
            Select::make('status')->label(__('app.fields.status'))->options(PoaStatus::class)->default(PoaStatus::ACTIVE)->required(),
            Select::make('lawyer_ids')
                ->label(__('clients.poa.lawyers'))
                ->multiple()
                ->options(fn () => User::lawyers()->pluck('name', 'id'))
                ->searchable(),
            Textarea::make('scope')->label(__('clients.poa.scope'))->columnSpanFull(),
            FileUpload::make('file_path')
                ->label(__('app.fields.file'))
                ->disk(config('firm.documents_disk'))
                ->directory('poa')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->downloadable()
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label(__('clients.poa.number'))->weight('bold'),
                TextColumn::make('notary_office')->label(__('clients.poa.notary_office')),
                TextColumn::make('issued_at')->label(__('clients.poa.issued_at'))->date(),
                TextColumn::make('expires_at')->label(__('clients.poa.expires_at'))->date()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : null),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
