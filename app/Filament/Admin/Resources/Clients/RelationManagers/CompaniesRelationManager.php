<?php

namespace App\Filament\Admin\Resources\Clients\RelationManagers;

use App\Filament\Admin\Resources\Companies\CompanyResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    protected static ?string $relatedResource = CompanyResource::class;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('companies.plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('companies.fields.name'))->weight('bold'),
                TextColumn::make('legal_form')->label(__('companies.fields.legal_form'))->badge()->color('gray'),
                TextColumn::make('commercial_register_no')->label(__('companies.fields.commercial_register_no')),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->headerActions([
                CreateAction::make()->url(fn () => CompanyResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => CompanyResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
