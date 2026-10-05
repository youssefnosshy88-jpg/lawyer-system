<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Expense;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 5;

    public function getHeading(): ?string
    {
        return __('dashboard.widgets.revenue');
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $payments = Payment::query()
            ->where('paid_at', '>=', $months->first())
            ->get(['amount', 'paid_at'])
            ->groupBy(fn (Payment $p) => Carbon::parse($p->paid_at)->format('Y-m'))
            ->map(fn ($g) => (float) $g->sum('amount'));

        $expenses = Expense::query()
            ->where('spent_at', '>=', $months->first())
            ->get(['amount', 'spent_at'])
            ->groupBy(fn (Expense $e) => Carbon::parse($e->spent_at)->format('Y-m'))
            ->map(fn ($g) => (float) $g->sum('amount'));

        return [
            'datasets' => [
                [
                    'label' => __('dashboard.chart.collected'),
                    'data' => $months->map(fn ($m) => $payments[$m->format('Y-m')] ?? 0)->values()->all(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                ],
                [
                    'label' => __('dashboard.chart.expenses'),
                    'data' => $months->map(fn ($m) => $expenses[$m->format('Y-m')] ?? 0)->values()->all(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.6)',
                ],
            ],
            'labels' => $months->map(fn ($m) => $m->translatedFormat('M Y'))->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
