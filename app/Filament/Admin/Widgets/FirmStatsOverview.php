<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\CompanyDeadline;
use App\Models\CompanyProcedure;
use App\Models\Hearing;
use App\Models\Invoice;
use App\Models\LegalCase;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FirmStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currency = config('firm.currency');

        $outstanding = (float) Invoice::outstanding()->selectRaw('COALESCE(SUM(total - paid_amount), 0) as b')->value('b');
        $overdue = Invoice::where('status', InvoiceStatus::OVERDUE)->count();

        return [
            Stat::make(__('dashboard.stats.active_cases'), LegalCase::active()->count())
                ->icon('heroicon-o-scale')
                ->color('primary'),
            Stat::make(__('dashboard.stats.hearings_week'), Hearing::upcoming()->whereBetween('scheduled_at', [now(), now()->addDays(7)])->count())
                ->description(__('dashboard.stats.hearings_today', ['count' => Hearing::whereDate('scheduled_at', today())->count()]))
                ->icon('heroicon-o-calendar-days')
                ->color('warning'),
            Stat::make(__('dashboard.stats.clients'), Client::where('is_active', true)->count())
                ->icon('heroicon-o-users'),
            Stat::make(__('dashboard.stats.open_procedures'), CompanyProcedure::open()->count())
                ->description(__('dashboard.stats.deadlines_month', ['count' => CompanyDeadline::pending()->whereDate('due_at', '<=', now()->addDays(30))->count()]))
                ->icon('heroicon-o-building-office-2')
                ->color('info'),
            Stat::make(__('dashboard.stats.outstanding'), number_format($outstanding, 0).' '.$currency)
                ->description(__('dashboard.stats.overdue_invoices', ['count' => $overdue]))
                ->descriptionColor($overdue > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-banknotes')
                ->color($overdue > 0 ? 'danger' : 'success'),
        ];
    }
}
