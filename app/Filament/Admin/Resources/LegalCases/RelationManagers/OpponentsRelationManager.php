<?php

namespace App\Filament\Admin\Resources\LegalCases\RelationManagers;

use App\Enums\ClientRole;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OpponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'opponents';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('cases.fields.opponents');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('opponents.fields.name'))->weight('bold'),
                TextColumn::make('type')->label(__('clients.fields.type'))->badge()->color('gray'),
                TextColumn::make('pivot.role')->label(__('opponents.fields.role'))->formatStateUsing(fn ($state) => $state ? ClientRole::tryFrom($state)?->getLabel() : '-'),
                TextColumn::make('lawyer_name')->label(__('opponents.fields.lawyer_name')),
                TextColumn::make('phone')->label(__('clients.fields.phone')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'national_id'])
                    ->schema(fn (AttachAction $action) => [
                        $action->getRecordSelect(),
                        Select::make('role')->label(__('opponents.fields.role'))->options(ClientRole::class)->default(ClientRole::DEFENDANT),
                    ]),
            ])
            ->recordActions([DetachAction::make()]);
    }
}
