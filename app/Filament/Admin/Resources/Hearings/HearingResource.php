<?php

namespace App\Filament\Admin\Resources\Hearings;

use App\Enums\HearingStatus;
use App\Enums\HearingType;
use App\Filament\Admin\Resources\Hearings\Pages\CreateHearing;
use App\Filament\Admin\Resources\Hearings\Pages\EditHearing;
use App\Filament\Admin\Resources\Hearings\Pages\ListHearings;
use App\Filament\Admin\Resources\LegalCases\LegalCaseResource;
use App\Models\Hearing;
use App\Models\LegalCase;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HearingResource extends Resource
{
    protected static ?string $model = Hearing::class;

    protected static ?string $slug = 'hearings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 21;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.cases');
    }

    public static function getModelLabel(): string
    {
        return __('hearings.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hearings.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::upcoming()->whereDate('scheduled_at', '<=', now()->addDays(7))->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->schema([
                    Select::make('legal_case_id')
                        ->label(__('hearings.fields.case'))
                        ->options(fn () => LegalCase::active()->with('client')->get()->mapWithKeys(fn (LegalCase $c) => [$c->id => "{$c->full_number} - {$c->title} ({$c->client?->name})"]))
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                    DateTimePicker::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->required()->seconds(false),
                    Select::make('type')->label(__('hearings.fields.type'))->options(HearingType::class)->default(HearingType::PLEADING)->required(),
                    Select::make('status')->label(__('app.fields.status'))->options(HearingStatus::class)->default(HearingStatus::SCHEDULED)->required(),
                    Select::make('lawyer_id')->label(__('hearings.fields.lawyer'))->options(fn () => User::lawyers()->pluck('name', 'id'))->searchable(),
                    TextInput::make('courtroom')->label(__('hearings.fields.courtroom')),
                    Toggle::make('visible_to_client')->label(__('app.fields.visible_to_client'))->default(true)->inline(false),
                    Textarea::make('requirements')->label(__('hearings.fields.requirements'))->rows(2)->columnSpanFull(),
                    Textarea::make('outcome')->label(__('hearings.fields.outcome'))->rows(2)->columnSpanFull(),
                    Textarea::make('decision')->label(__('hearings.fields.decision'))->rows(2)->columnSpanFull(),
                    DatePicker::make('next_hearing_at')->label(__('hearings.fields.next_hearing_at')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['legalCase.client', 'legalCase.court', 'lawyer']))
            ->columns([
                TextColumn::make('scheduled_at')->label(__('hearings.fields.scheduled_at'))->dateTime('D Y-m-d H:i')->sortable()->weight('bold'),
                TextColumn::make('legalCase.full_number')->label(__('cases.fields.case_number')),
                TextColumn::make('legalCase.title')->label(__('hearings.fields.case'))->limit(30)->url(fn (Hearing $r) => LegalCaseResource::getUrl('view', ['record' => $r->legal_case_id])),
                TextColumn::make('legalCase.client.name')->label(__('cases.fields.client'))->limit(25),
                TextColumn::make('legalCase.court.display_name')->label(__('cases.fields.court'))->limit(25),
                TextColumn::make('courtroom')->label(__('hearings.fields.courtroom'))->toggleable(),
                TextColumn::make('type')->label(__('hearings.fields.type'))->badge()->color('gray'),
                TextColumn::make('lawyer.name')->label(__('hearings.fields.lawyer')),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(HearingStatus::class),
                SelectFilter::make('lawyer_id')->label(__('hearings.fields.lawyer'))->relationship('lawyer', 'name'),
                Filter::make('scheduled_at')
                    ->schema([
                        DatePicker::make('from')->label(__('app.filters.from')),
                        DatePicker::make('until')->label(__('app.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('scheduled_at', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('scheduled_at', '<=', $d))),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHearings::route('/'),
            'create' => CreateHearing::route('/create'),
            'edit' => EditHearing::route('/{record}/edit'),
        ];
    }
}
