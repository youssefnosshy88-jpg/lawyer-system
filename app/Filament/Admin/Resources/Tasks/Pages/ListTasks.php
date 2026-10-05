<?php

namespace App\Filament\Admin\Resources\Tasks\Pages;

use App\Filament\Admin\Resources\Tasks\TaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'mine' => Tab::make(__('tasks.tabs.mine'))
                ->modifyQueryUsing(fn (Builder $q) => $q->where('assigned_to', auth()->id())),
            'all' => Tab::make(__('tasks.tabs.all')),
        ];
    }
}
