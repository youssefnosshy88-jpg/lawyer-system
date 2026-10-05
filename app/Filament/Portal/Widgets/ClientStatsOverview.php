<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Client;
use App\Models\Hearing;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var Client $client */
        $client = auth()->user()->client;

        return [
            Stat::make(__('dashboard.stats.active_cases'), $client->cases()->active()->where('visible_to_client', true)->count())->icon('heroicon-o-scale'),
            Stat::make(__('dashboard.stats.hearings_week'), Hearing::upcoming()->where('visible_to_client', true)
                ->whereHas('legalCase', fn ($q) => $q->where('client_id', $client->id)->where('visible_to_client', true))
                ->whereBetween('scheduled_at', [now(), now()->addDays(7)])->count())->icon('heroicon-o-calendar-days')->color('warning'),
            Stat::make(__('companies.plural'), $client->companies()->count())->icon('heroicon-o-building-office-2'),
            Stat::make(__('dashboard.stats.outstanding'), number_format($client->outstanding_balance, 0).' '.config('firm.currency'))->icon('heroicon-o-banknotes')->color($client->outstanding_balance > 0 ? 'danger' : 'success'),
        ];
    }
}
