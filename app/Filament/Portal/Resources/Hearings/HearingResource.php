<?php

namespace App\Filament\Portal\Resources\Hearings;

use App\Filament\Portal\Resources\Hearings\Pages\ListHearings;
use App\Models\Hearing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HearingResource extends Resource
{
    protected static ?string $model = Hearing::class;

    protected static ?string $slug = 'hearings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('hearings.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hearings.plural');
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
        $clientId = auth()->user()->client_id;

        return parent::getEloquentQuery()
            ->where('visible_to_client', true)
            ->whereHas('legalCase', fn ($q) => $q->where('client_id', $clientId)->where('visible_to_client', true))
            ->with(['legalCase.court']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->dateTime('D Y-m-d H:i')->sortable()->weight('bold'),
                TextColumn::make('legalCase.full_number')->label(__('cases.fields.case_number')),
                TextColumn::make('legalCase.title')->label(__('hearings.fields.case'))->limit(35),
                TextColumn::make('legalCase.court.display_name')->label(__('cases.fields.court')),
                TextColumn::make('type')->label(__('hearings.fields.type'))->badge()->color('gray'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
                TextColumn::make('outcome')->label(__('hearings.fields.outcome'))->limit(40)->wrap(),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListHearings::route('/')];
    }
}
