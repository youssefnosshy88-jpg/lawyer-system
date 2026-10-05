<?php

namespace App\Filament\Admin\Resources\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Filament\Admin\Resources\Tasks\Pages\CreateTask;
use App\Filament\Admin\Resources\Tasks\Pages\EditTask;
use App\Filament\Admin\Resources\Tasks\Pages\ListTasks;
use App\Models\Company;
use App\Models\CompanyProcedure;
use App\Models\LegalCase;
use App\Models\Task;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $slug = 'tasks';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?int $navigationSort = 60;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.work');
    }

    public static function getModelLabel(): string
    {
        return __('tasks.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tasks.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::open()->where('assigned_to', auth()->id())->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('title')->label(__('tasks.fields.title'))->required()->columnSpanFull(),
                MorphToSelect::make('taskable')
                    ->label(__('tasks.fields.related_to'))
                    ->types([
                        MorphToSelect\Type::make(LegalCase::class)->label(__('cases.singular'))->titleAttribute('title'),
                        MorphToSelect\Type::make(Company::class)->label(__('companies.singular'))->titleAttribute('name'),
                        MorphToSelect\Type::make(CompanyProcedure::class)->label(__('procedures.singular'))->titleAttribute('reference'),
                    ])
                    ->searchable()
                    ->columnSpanFull(),
                Select::make('assigned_to')->label(__('tasks.fields.assigned_to'))->options(fn () => User::staff()->pluck('name', 'id'))->searchable()->default(auth()->id())->required(),
                DateTimePicker::make('due_at')->label(__('tasks.fields.due_at'))->seconds(false),
                Select::make('priority')->label(__('tasks.fields.priority'))->options(TaskPriority::class)->default(TaskPriority::NORMAL)->required(),
                Select::make('status')->label(__('app.fields.status'))->options(TaskStatus::class)->default(TaskStatus::TODO)->required(),
                Textarea::make('description')->label(__('tasks.fields.description'))->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['assignee', 'taskable']))
            ->columns([
                TextColumn::make('title')->label(__('tasks.fields.title'))->searchable()->weight('bold')->limit(40),
                TextColumn::make('taskable_type')->label(__('tasks.fields.related_to'))->formatStateUsing(fn (Task $r) => static::describeTaskable($r))->placeholder('-'),
                TextColumn::make('assignee.name')->label(__('tasks.fields.assigned_to')),
                TextColumn::make('due_at')->label(__('tasks.fields.due_at'))->dateTime('Y-m-d H:i')->sortable()
                    ->color(fn (Task $r) => $r->due_at?->isPast() && $r->status !== TaskStatus::DONE ? 'danger' : null),
                TextColumn::make('priority')->label(__('tasks.fields.priority'))->badge()->sortable(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(TaskStatus::class)->multiple()->default([TaskStatus::TODO->value, TaskStatus::IN_PROGRESS->value]),
                SelectFilter::make('priority')->label(__('tasks.fields.priority'))->options(TaskPriority::class),
                SelectFilter::make('assigned_to')->label(__('tasks.fields.assigned_to'))->relationship('assignee', 'name'),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label(__('tasks.actions.complete'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Task $r) => $r->status !== TaskStatus::DONE)
                    ->action(fn (Task $r) => $r->update(['status' => TaskStatus::DONE])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('complete')
                        ->label(__('tasks.actions.complete'))
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->action(fn (Collection $records) => $records->each->update(['status' => TaskStatus::DONE]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_at');
    }

    public static function describeTaskable(Task $task): string
    {
        $owner = $task->taskable;

        return match (true) {
            $owner instanceof LegalCase => __('cases.singular').': '.$owner->full_number,
            $owner instanceof Company => __('companies.singular').': '.$owner->name,
            $owner instanceof CompanyProcedure => __('procedures.singular').': '.$owner->reference,
            default => '-',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
