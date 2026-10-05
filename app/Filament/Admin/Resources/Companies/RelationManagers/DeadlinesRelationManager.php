<?php

namespace App\Filament\Admin\Resources\Companies\RelationManagers;

use App\Enums\DeadlineStatus;
use App\Enums\DeadlineType;
use App\Models\CompanyDeadline;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeadlinesRelationManager extends RelationManager
{
    protected static string $relationship = 'deadlines';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('deadlines.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->label(__('deadlines.fields.type'))->options(DeadlineType::class)->default(DeadlineType::COMMERCIAL_REGISTER)->required(),
            TextInput::make('title')->label(__('deadlines.fields.title'))->required(),
            DatePicker::make('due_at')->label(__('deadlines.fields.due_at'))->required(),
            Select::make('status')->label(__('app.fields.status'))->options(DeadlineStatus::class)->default(DeadlineStatus::PENDING)->required(),
            Toggle::make('recurring_yearly')->label(__('deadlines.fields.recurring_yearly'))->inline(false),
            Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('due_at')->label(__('deadlines.fields.due_at'))->date()->sortable()->weight('bold')
                    ->color(fn (CompanyDeadline $r) => $r->status !== DeadlineStatus::DONE && $r->due_at->isPast() ? 'danger' : null),
                TextColumn::make('type')->label(__('deadlines.fields.type'))->badge()->color('gray'),
                TextColumn::make('title')->label(__('deadlines.fields.title')),
                IconColumn::make('recurring_yearly')->label(__('deadlines.fields.recurring_yearly'))->boolean(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                Action::make('done')
                    ->label(__('deadlines.actions.mark_done'))
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (CompanyDeadline $r) => $r->status !== DeadlineStatus::DONE)
                    ->requiresConfirmation()
                    ->action(fn (CompanyDeadline $r) => $r->markDone()),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('due_at');
    }
}
