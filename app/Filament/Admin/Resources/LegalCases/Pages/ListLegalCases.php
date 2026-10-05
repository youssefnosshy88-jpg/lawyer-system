<?php

namespace App\Filament\Admin\Resources\LegalCases\Pages;

use App\Enums\CaseStatus;
use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLegalCases extends ListRecords
{
    protected static string $resource = LegalCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make(__('cases.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->active()),
            'mine' => Tab::make(__('cases.tabs.mine'))
                ->modifyQueryUsing(fn (Builder $query) => $query->active()->where(fn ($q) => $q
                    ->where('lead_lawyer_id', auth()->id())
                    ->orWhereHas('lawyers', fn ($l) => $l->where('users.id', auth()->id())))),
            'closed' => Tab::make(__('cases.tabs.closed'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [CaseStatus::CLOSED, CaseStatus::ARCHIVED])),
            'all' => Tab::make(__('cases.tabs.all')),
        ];
    }
}
