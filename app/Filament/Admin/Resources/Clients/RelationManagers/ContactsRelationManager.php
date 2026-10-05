<?php

namespace App\Filament\Admin\Resources\Clients\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('clients.contacts.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('clients.contacts.name'))->required(),
            TextInput::make('position')->label(__('clients.contacts.position')),
            TextInput::make('phone')->label(__('clients.fields.phone'))->tel(),
            TextInput::make('email')->label(__('clients.fields.email'))->email(),
            Toggle::make('is_primary')->label(__('clients.contacts.is_primary')),
            Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('clients.contacts.name')),
                TextColumn::make('position')->label(__('clients.contacts.position')),
                TextColumn::make('phone')->label(__('clients.fields.phone'))->copyable(),
                TextColumn::make('email')->label(__('clients.fields.email')),
                IconColumn::make('is_primary')->label(__('clients.contacts.is_primary'))->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
