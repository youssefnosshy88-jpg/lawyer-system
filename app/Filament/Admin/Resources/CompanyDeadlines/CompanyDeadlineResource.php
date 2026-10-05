<?php

namespace App\Filament\Admin\Resources\CompanyDeadlines;

use App\Enums\DeadlineStatus;
use App\Enums\DeadlineType;
use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Filament\Admin\Resources\CompanyDeadlines\Pages\CreateCompanyDeadline;
use App\Filament\Admin\Resources\CompanyDeadlines\Pages\EditCompanyDeadline;
use App\Filament\Admin\Resources\CompanyDeadlines\Pages\ListCompanyDeadlines;
use App\Models\CompanyDeadline;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CompanyDeadlineResource extends Resource
{
    protected static ?string $model = CompanyDeadline::class;

    protected static ?string $slug = 'company-deadlines';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?int $navigationSort = 52;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.companies');
    }

    public static function getModelLabel(): string
    {
        return __('deadlines.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('deadlines.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::pending()->whereDate('due_at', '<=', now()->addDays(30))->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('company_id')->label(__('companies.singular'))->relationship('company', 'name')->searchable()->preload()->required(),
                Select::make('type')->label(__('deadlines.fields.type'))->options(DeadlineType::class)->default(DeadlineType::COMMERCIAL_REGISTER)->required(),
                TextInput::make('title')->label(__('deadlines.fields.title'))->required(),
                DatePicker::make('due_at')->label(__('deadlines.fields.due_at'))->required(),
                Select::make('status')->label(__('app.fields.status'))->options(DeadlineStatus::class)->default(DeadlineStatus::PENDING)->required(),
                Toggle::make('recurring_yearly')->label(__('deadlines.fields.recurring_yearly'))->inline(false),
                Textarea::make('notes')->label(__('app.fields.notes'))->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with('company.client'))
            ->columns([
                TextColumn::make('due_at')->label(__('deadlines.fields.due_at'))->date()->sortable()->weight('bold')
                    ->color(fn (CompanyDeadline $r) => $r->status !== DeadlineStatus::DONE && $r->due_at->isPast() ? 'danger' : null),
                TextColumn::make('company.name')->label(__('companies.singular'))->searchable()->url(fn (CompanyDeadline $r) => CompanyResource::getUrl('view', ['record' => $r->company_id])),
                TextColumn::make('company.client.name')->label(__('cases.fields.client'))->toggleable(),
                TextColumn::make('type')->label(__('deadlines.fields.type'))->badge()->color('gray'),
                TextColumn::make('title')->label(__('deadlines.fields.title'))->searchable(),
                IconColumn::make('recurring_yearly')->label(__('deadlines.fields.recurring_yearly'))->boolean(),
                TextColumn::make('status')->label(__('app.fields.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('app.fields.status'))->options(DeadlineStatus::class)->default(DeadlineStatus::PENDING->value),
                SelectFilter::make('type')->label(__('deadlines.fields.type'))->options(DeadlineType::class),
            ])
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

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyDeadlines::route('/'),
            'create' => CreateCompanyDeadline::route('/create'),
            'edit' => EditCompanyDeadline::route('/{record}/edit'),
        ];
    }
}
