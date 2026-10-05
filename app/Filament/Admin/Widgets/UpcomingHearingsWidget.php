<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use App\Models\Hearing;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingHearingsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('dashboard.widgets.upcoming_hearings'))
            ->query(fn (): Builder => Hearing::query()
                ->upcoming()
                ->whereDate('scheduled_at', '<=', now()->addDays(14))
                ->with(['legalCase.client', 'legalCase.court', 'lawyer']))
            ->columns([
                TextColumn::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->dateTime('D Y-m-d H:i')->weight('bold')
                    ->color(fn (Hearing $r) => $r->scheduled_at->isToday() ? 'danger' : ($r->scheduled_at->isTomorrow() ? 'warning' : null)),
                TextColumn::make('legalCase.full_number')->label(__('cases.fields.case_number')),
                TextColumn::make('legalCase.title')->label(__('hearings.fields.case'))->limit(30)->url(fn (Hearing $r) => LegalCaseResource::getUrl('view', ['record' => $r->legal_case_id])),
                TextColumn::make('legalCase.client.name')->label(__('cases.fields.client'))->limit(25),
                TextColumn::make('legalCase.court.display_name')->label(__('cases.fields.court'))->limit(25),
                TextColumn::make('lawyer.name')->label(__('hearings.fields.lawyer')),
                TextColumn::make('requirements')->label(__('hearings.fields.requirements'))->limit(30)->wrap(),
            ])
            ->defaultSort('scheduled_at')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
