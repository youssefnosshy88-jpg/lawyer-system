<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Models\CompanyDeadline;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingDeadlinesWidget extends TableWidget
{
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('dashboard.widgets.company_deadlines'))
            ->query(fn (): Builder => CompanyDeadline::query()->pending()->whereDate('due_at', '<=', now()->addDays(60))->with('company'))
            ->columns([
                TextColumn::make('due_at')->label(__('deadlines.fields.due_at'))->date()->weight('bold')->color(fn (CompanyDeadline $r) => $r->due_at->isPast() ? 'danger' : ($r->due_at->diffInDays(now()) <= 14 ? 'warning' : null)),
                TextColumn::make('company.name')->label(__('companies.singular'))->limit(25)->url(fn (CompanyDeadline $r) => CompanyResource::getUrl('view', ['record' => $r->company_id])),
                TextColumn::make('title')->label(__('deadlines.fields.title'))->limit(30),
                TextColumn::make('type')->label(__('deadlines.fields.type'))->badge()->color('gray'),
            ])
            ->defaultSort('due_at')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
