<?php

namespace App\Filament\Admin\Resources\Hearings\Pages;

use App\Filament\Admin\Resources\Hearings\HearingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListHearings extends ListRecords
{
    protected static string $resource = HearingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'today' => Tab::make(__('hearings.tabs.today'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('scheduled_at', today())),
            'week' => Tab::make(__('hearings.tabs.week'))
                ->modifyQueryUsing(fn (Builder $query) => $query->upcoming()->whereBetween('scheduled_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])),
            'upcoming' => Tab::make(__('hearings.tabs.upcoming'))
                ->modifyQueryUsing(fn (Builder $query) => $query->upcoming()),
            'mine' => Tab::make(__('hearings.tabs.mine'))
                ->modifyQueryUsing(fn (Builder $query) => $query->upcoming()->where('lawyer_id', auth()->id())),
            'all' => Tab::make(__('hearings.tabs.all')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'week';
    }
}
