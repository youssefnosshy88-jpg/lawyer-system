<?php

namespace App\Filament\Admin\Resources\LegalCases\RelationManagers;

use App\Enums\ActivityType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('cases.activities.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label(__('cases.activities.title_field'))->required()->columnSpanFull(),
            Select::make('type')->label(__('cases.activities.type'))->options(ActivityType::class)->default(ActivityType::MEMO)->required(),
            DateTimePicker::make('occurred_at')->label(__('cases.activities.occurred_at'))->default(now())->seconds(false)->required(),
            TextInput::make('hours_spent')->label(__('cases.activities.hours_spent'))->numeric()->step(0.25),
            Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->inline(false),
            Textarea::make('body')->label(__('cases.activities.body'))->rows(4)->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')->label(__('cases.activities.occurred_at'))->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('type')->label(__('cases.activities.type'))->badge()->color('gray'),
                TextColumn::make('title')->label(__('cases.activities.title_field'))->weight('bold')->description(fn ($record) => str($record->body)->limit(80)),
                TextColumn::make('user.name')->label(__('cases.activities.user')),
                TextColumn::make('hours_spent')->label(__('cases.activities.hours_spent'))->alignCenter(),
                IconColumn::make('visible_to_client')->label(__('app.fields.visible_to_client'))->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->mutateDataUsing(fn (array $data) => $data + ['user_id' => auth()->id()]),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('occurred_at', 'desc');
    }
}
