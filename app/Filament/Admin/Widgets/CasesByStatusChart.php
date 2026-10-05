<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\CaseStatus;
use App\Models\LegalCase;
use Filament\Widgets\ChartWidget;

class CasesByStatusChart extends ChartWidget
{
    protected static ?int $sort = 6;

    public function getHeading(): ?string
    {
        return __('dashboard.widgets.cases_by_status');
    }

    protected function getData(): array
    {
        $counts = LegalCase::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $statuses = collect(CaseStatus::cases());

        return [
            'datasets' => [[
                'data' => $statuses->map(fn (CaseStatus $s) => (int) ($counts[$s->value] ?? 0))->values()->all(),
                'backgroundColor' => ['#3b82f6', '#f59e0b', '#9ca3af', '#10b981', '#f97316', '#6b7280', '#374151'],
            ]],
            'labels' => $statuses->map(fn (CaseStatus $s) => $s->getLabel())->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
