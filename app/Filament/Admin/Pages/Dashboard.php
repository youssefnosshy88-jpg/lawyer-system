<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\CasesByStatusChart;
use App\Filament\Admin\Widgets\FirmStatsOverview;
use App\Filament\Admin\Widgets\MyTasksWidget;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Filament\Admin\Widgets\UpcomingDeadlinesWidget;
use App\Filament\Admin\Widgets\UpcomingHearingsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationLabel(): string
    {
        return __('app.nav.dashboard');
    }

    public function getTitle(): string
    {
        return __('app.nav.dashboard');
    }

    public function getWidgets(): array
    {
        return [
            FirmStatsOverview::class,
            UpcomingHearingsWidget::class,
            MyTasksWidget::class,
            UpcomingDeadlinesWidget::class,
            RevenueChart::class,
            CasesByStatusChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
