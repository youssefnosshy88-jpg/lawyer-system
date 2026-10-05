<?php

namespace App\Filament\Admin\Resources\LegalCases\RelationManagers;

use App\Enums\HearingStatus;
use App\Enums\HearingType;
use App\Models\Hearing;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HearingsRelationManager extends RelationManager
{
    protected static string $relationship = 'hearings';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('hearings.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->required()->seconds(false)->default(now()->addWeek()->setTime(9, 0)),
            Select::make('type')->label(__('hearings.fields.type'))->options(HearingType::class)->default(HearingType::PLEADING)->required(),
            Select::make('status')->label(__('app.fields.status'))->options(HearingStatus::class)->default(HearingStatus::SCHEDULED)->required(),
            Select::make('lawyer_id')->label(__('hearings.fields.lawyer'))->options(fn () => User::lawyers()->pluck('name', 'id'))->searchable()->default(fn () => $this->getOwnerRecord()->lead_lawyer_id),
            TextInput::make('courtroom')->label(__('hearings.fields.courtroom')),
            Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->default(true)->inline(false),
            Textarea::make('requirements')->label(__('hearings.fields.requirements'))->rows(2)->columnSpanFull(),
            Textarea::make('outcome')->label(__('hearings.fields.outcome'))->rows(2)->columnSpanFull(),
            Textarea::make('decision')->label(__('hearings.fields.decision'))->rows(2)->columnSpanFull(),
            DatePicker::make('next_hearing_at')->label(__('hearings.fields.next_hearing_at')),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->dateTime('Y-m-d H:i')->sortable()->weight('bold'),
                TextColumn::make('type')->label(__('hearings.fields.type'))->badge()->color('gray'),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
                TextColumn::make('lawyer.name')->label(__('hearings.fields.lawyer')),
                TextColumn::make('courtroom')->label(__('hearings.fields.courtroom'))->toggleable(),
                TextColumn::make('outcome')->label(__('hearings.fields.outcome'))->limit(40)->wrap(),
                TextColumn::make('next_hearing_at')->label(__('hearings.fields.next_hearing_at'))->date(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                Action::make('record_outcome')
                    ->label(__('hearings.actions.record_outcome'))
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->color('success')
                    ->visible(fn (Hearing $record) => $record->status === HearingStatus::SCHEDULED)
                    ->schema([
                        Select::make('status')->label(__('app.fields.status'))->options([
                            HearingStatus::HELD->value => HearingStatus::HELD->getLabel(),
                            HearingStatus::POSTPONED->value => HearingStatus::POSTPONED->getLabel(),
                            HearingStatus::CANCELLED->value => HearingStatus::CANCELLED->getLabel(),
                        ])->default(HearingStatus::HELD->value)->required(),
                        Textarea::make('outcome')->label(__('hearings.fields.outcome'))->required()->rows(3),
                        Textarea::make('decision')->label(__('hearings.fields.decision'))->rows(2),
                        DateTimePicker::make('next_hearing_at')->label(__('hearings.fields.schedule_next'))->seconds(false),
                        Toggle::make('create_next')->label(__('hearings.actions.create_next'))->default(true),
                    ])
                    ->action(function (Hearing $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'outcome' => $data['outcome'],
                            'decision' => $data['decision'] ?? null,
                            'next_hearing_at' => $data['next_hearing_at'] ?? null,
                        ]);

                        if (! empty($data['create_next']) && ! empty($data['next_hearing_at'])) {
                            $record->legalCase->hearings()->create([
                                'scheduled_at' => $data['next_hearing_at'],
                                'type' => $record->type,
                                'status' => HearingStatus::SCHEDULED,
                                'lawyer_id' => $record->lawyer_id,
                                'courtroom' => $record->courtroom,
                            ]);
                        }
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }
}
