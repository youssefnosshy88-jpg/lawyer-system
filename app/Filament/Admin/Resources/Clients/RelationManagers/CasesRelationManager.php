<?php

namespace App\Filament\Admin\Resources\Clients\RelationManagers;

use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CasesRelationManager extends RelationManager
{
    protected static string $relationship = 'cases';

    protected static ?string $relatedResource = LegalCaseResource::class;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('cases.plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->weight('bold'),
                TextColumn::make('full_number')->label(__('cases.fields.case_number')),
                TextColumn::make('title')->label(__('cases.fields.title'))->limit(40),
                TextColumn::make('court.display_name')->label(__('cases.fields.court')),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
                TextColumn::make('nextHearing.scheduled_at')->label(__('cases.fields.next_hearing'))->dateTime('Y-m-d H:i'),
            ])
            ->headerActions([
                CreateAction::make()->url(fn () => LegalCaseResource::getUrl('create', ['client_id' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => LegalCaseResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
