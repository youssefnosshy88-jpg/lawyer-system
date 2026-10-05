<?php

namespace App\Filament\Admin\RelationManagers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

abstract class BaseTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('tasks.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label(__('tasks.fields.title'))->required()->columnSpanFull(),
            Select::make('assigned_to')->label(__('tasks.fields.assigned_to'))->options(fn () => User::staff()->pluck('name', 'id'))->searchable()->default(auth()->id()),
            DateTimePicker::make('due_at')->label(__('tasks.fields.due_at'))->seconds(false),
            Select::make('priority')->label(__('tasks.fields.priority'))->options(TaskPriority::class)->default(TaskPriority::NORMAL)->required(),
            Select::make('status')->label(__('app.fields.status'))->options(TaskStatus::class)->default(TaskStatus::TODO)->required(),
            Textarea::make('description')->label(__('tasks.fields.description'))->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('tasks.fields.title'))->weight('bold')->searchable(),
                TextColumn::make('assignee.name')->label(__('tasks.fields.assigned_to')),
                TextColumn::make('due_at')->label(__('tasks.fields.due_at'))->dateTime('Y-m-d H:i')->color(fn (Task $r) => $r->due_at?->isPast() && $r->status !== TaskStatus::DONE ? 'danger' : null),
                TextColumn::make('priority')->label(__('tasks.fields.priority'))->badge(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                Action::make('complete')
                    ->label(__('tasks.actions.complete'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Task $record) => $record->status !== TaskStatus::DONE)
                    ->action(fn (Task $record) => $record->update(['status' => TaskStatus::DONE])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('due_at');
    }
}
