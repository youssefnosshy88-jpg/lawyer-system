<?php

namespace App\Filament\Admin\Resources\FeeAgreements;

use App\Enums\FeeType;
use App\Filament\Admin\Resources\FeeAgreements\Pages\CreateFeeAgreement;
use App\Filament\Admin\Resources\FeeAgreements\Pages\EditFeeAgreement;
use App\Filament\Admin\Resources\FeeAgreements\Pages\ListFeeAgreements;
use App\Models\FeeAgreement;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FeeAgreementResource extends Resource
{
    protected static ?string $model = FeeAgreement::class;

    protected static ?string $slug = 'fee-agreements';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 40;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('app.nav.billing');
    }

    public static function getModelLabel(): string
    {
        return __('billing.fee_agreements.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('billing.fee_agreements.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                Select::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable()->preload()->required()->live(),
                Select::make('legal_case_id')->label(__('cases.singular'))
                    ->relationship('legalCase', 'title', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()->preload(),
                Select::make('company_id')->label(__('companies.singular'))
                    ->relationship('company', 'name', fn (Builder $q, Get $get) => $q->when($get('client_id'), fn ($q, $id) => $q->where('client_id', $id)))
                    ->searchable()->preload(),
                Select::make('type')->label(__('billing.fee_agreements.type'))->options(FeeType::class)->default(FeeType::FIXED)->required()->live(),
                TextInput::make('total_amount')->label(__('billing.fee_agreements.total_amount'))->numeric()->required()->default(0)->suffix(config('firm.currency')),
                TextInput::make('advance_amount')->label(__('billing.fee_agreements.advance_amount'))->numeric()->suffix(config('firm.currency')),
                TextInput::make('hourly_rate')->label(__('billing.fee_agreements.hourly_rate'))->numeric()->visible(fn (Get $get) => in_array($get('type'), [FeeType::HOURLY, FeeType::HOURLY->value], true)),
                TextInput::make('success_percentage')->label(__('billing.fee_agreements.success_percentage'))->numeric()->suffix('%')->visible(fn (Get $get) => in_array($get('type'), [FeeType::PERCENTAGE, FeeType::PERCENTAGE->value], true)),
                TextInput::make('installments_count')->label(__('billing.fee_agreements.installments_count'))->numeric()->visible(fn (Get $get) => in_array($get('type'), [FeeType::INSTALLMENTS, FeeType::INSTALLMENTS->value], true)),
                DatePicker::make('agreed_at')->label(__('billing.fee_agreements.agreed_at'))->default(now()),
                Toggle::make('is_active')->label(__('app.fields.is_active'))->default(true)->inline(false),
                FileUpload::make('file_path')->label(__('app.fields.file'))->disk(config('firm.documents_disk'))->directory('fee-agreements')->downloadable(),
                Textarea::make('terms')->label(__('billing.fee_agreements.terms'))->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with(['client', 'legalCase', 'company']))
            ->columns([
                TextColumn::make('reference')->label(__('cases.fields.reference'))->searchable()->weight('bold'),
                TextColumn::make('client.name')->label(__('cases.fields.client'))->searchable(),
                TextColumn::make('legalCase.full_number')->label(__('cases.singular'))->placeholder('-'),
                TextColumn::make('company.name')->label(__('companies.singular'))->placeholder('-')->toggleable(),
                TextColumn::make('type')->label(__('billing.fee_agreements.type'))->badge()->color('gray'),
                TextColumn::make('total_amount')->label(__('billing.fee_agreements.total_amount'))->money(config('firm.currency'))->sortable(),
                TextColumn::make('invoiced_amount')->label(__('billing.fee_agreements.invoiced'))->money(config('firm.currency')),
                TextColumn::make('remaining_amount')->label(__('billing.fee_agreements.remaining'))->money(config('firm.currency'))->color('warning'),
                TextColumn::make('agreed_at')->label(__('billing.fee_agreements.agreed_at'))->date()->sortable(),
                IconColumn::make('is_active')->label(__('app.fields.is_active'))->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('billing.fee_agreements.type'))->options(FeeType::class),
                SelectFilter::make('client_id')->label(__('cases.fields.client'))->relationship('client', 'name')->searchable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeeAgreements::route('/'),
            'create' => CreateFeeAgreement::route('/create'),
            'edit' => EditFeeAgreement::route('/{record}/edit'),
        ];
    }
}
