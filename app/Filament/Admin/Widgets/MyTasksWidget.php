<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\TaskStatus;
use App\Filament\Admin\Resources\Tasks\TaskResource;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MyTasksWidget extends TableWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('dashboard.widgets.my_tasks'))
            ->query(fn (): Builder => Task::query()->open()->where('assigned_to', auth()->id())->with('taskable'))
            ->columns([
                TextColumn::make('title')->label(__('tasks.fields.title'))->weight('bold')->limit(35)->description(fn (Task $r) => TaskResource::describeTaskable($r)),
                TextColumn::make('due_at')->label(__('tasks.fields.due_at'))->dateTime('Y-m-d')->color(fn (Task $r) => $r->due_at?->isPast() ? 'danger' : null),
                TextColumn::make('priority')->label(__('tasks.fields.priority'))->badge(),
            ])
            ->recordActions([
                Action::make('complete')
                    ->hiddenLabel()
                    ->tooltip(__('tasks.actions.complete'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->action(fn (Task $r) => $r->update(['status' => TaskStatus::DONE])),
            ])
            ->defaultSort('due_at')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
