<?php

namespace App\Filament\Admin\Resources\Companies\RelationManagers;

use App\Enums\PartnerRole;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('companies.partners.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('companies.partners.name'))->required(),
            Select::make('role')->label(__('companies.partners.role'))->options(PartnerRole::class)->default(PartnerRole::PARTNER)->required(),
            TextInput::make('nationality')->label(__('clients.fields.nationality'))->default('مصري'),
            Select::make('id_type')->label(__('companies.partners.id_type'))->options(__('companies.partners.id_types'))->default('national_id'),
            TextInput::make('id_number')->label(__('companies.partners.id_number')),
            TextInput::make('share_percentage')->label(__('companies.partners.share_percentage'))->numeric()->suffix('%')->maxValue(100),
            TextInput::make('shares_count')->label(__('companies.partners.shares_count'))->numeric(),
            TextInput::make('share_value')->label(__('companies.partners.share_value'))->numeric(),
            Toggle::make('is_signatory')->label(__('companies.partners.is_signatory'))->inline(false),
            TextInput::make('phone')->label(__('clients.fields.phone'))->tel(),
            TextInput::make('email')->label(__('clients.fields.email'))->email(),
            TextInput::make('address')->label(__('clients.fields.address')),
            Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('companies.partners.name'))->weight('bold')->searchable(),
                TextColumn::make('role')->label(__('companies.partners.role'))->badge()->color('gray'),
                TextColumn::make('nationality')->label(__('clients.fields.nationality')),
                TextColumn::make('id_number')->label(__('companies.partners.id_number')),
                TextColumn::make('share_percentage')->label(__('companies.partners.share_percentage'))->suffix('%')->summarize(Sum::make()->suffix('%')),
                TextColumn::make('share_value')->label(__('companies.partners.share_value'))->money(fn () => $this->getOwnerRecord()->currency),
                IconColumn::make('is_signatory')->label(__('companies.partners.is_signatory'))->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
