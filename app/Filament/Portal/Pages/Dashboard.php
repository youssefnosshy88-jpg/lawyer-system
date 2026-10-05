<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Widgets\ClientStatsOverview;
use App\Filament\Portal\Widgets\ClientUpcomingHearings;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationLabel(): string
    {
        return __('app.nav.dashboard');
    }

    public function getTitle(): string
    {
        return __('app.portal.welcome', ['name' => auth()->user()?->name]);
    }

    public function getWidgets(): array
    {
        return [
            ClientStatsOverview::class,
            ClientUpcomingHearings::class,
        ];
    }
}
