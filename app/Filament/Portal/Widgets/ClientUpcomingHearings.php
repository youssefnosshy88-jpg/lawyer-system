<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Hearing;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ClientUpcomingHearings extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $clientId = auth()->user()->client_id;

        return $table
            ->heading(__('dashboard.widgets.upcoming_hearings'))
            ->query(fn (): Builder => Hearing::query()
                ->upcoming()
                ->where('visible_to_client', true)
                ->whereHas('legalCase', fn ($q) => $q->where('client_id', $clientId)->where('visible_to_client', true))
                ->with(['legalCase.court']))
            ->columns([
                TextColumn::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->dateTime('D Y-m-d H:i')->weight('bold'),
                TextColumn::make('legalCase.full_number')->label(__('cases.fields.case_number')),
                TextColumn::make('legalCase.title')->label(__('hearings.fields.case')),
                TextColumn::make('legalCase.court.display_name')->label(__('cases.fields.court')),
                TextColumn::make('type')->label(__('hearings.fields.type'))->badge()->color('gray'),
            ])
            ->defaultSort('scheduled_at')
            ->paginated(false);
    }
}
