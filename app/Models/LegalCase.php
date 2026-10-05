<?php

namespace App\Models;

use App\Enums\CaseDegree;
use App\Enums\CaseStatus;
use App\Enums\ClientRole;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalCase extends Model
{
    use GeneratesReference, HasFactory, SoftDeletes;

    protected string $referencePrefix = 'CS';

    protected $fillable = [
        'reference', 'case_number', 'case_year', 'title', 'client_id', 'client_role', 'case_type_id',
        'court_id', 'circuit', 'degree', 'lead_lawyer_id', 'status', 'subject', 'description',
        'claim_amount', 'filed_at', 'closed_at', 'judgment_summary', 'visible_to_client', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => CaseStatus::class,
            'client_role' => ClientRole::class,
            'degree' => CaseDegree::class,
            'claim_amount' => 'decimal:2',
            'filed_at' => 'date',
            'closed_at' => 'date',
            'visible_to_client' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function leadLawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_lawyer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lawyers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'case_lawyer');
    }

    public function opponents(): BelongsToMany
    {
        return $this->belongsToMany(Opponent::class, 'case_opponent')->withPivot('role');
    }

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class)->orderBy('scheduled_at');
    }

    public function nextHearing(): HasOne
    {
        return $this->hasOne(Hearing::class)
            ->ofMany(['scheduled_at' => 'min'], fn (Builder $query) => $query
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', now()));
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CaseActivity::class)->latest('occurred_at');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function feeAgreements(): HasMany
    {
        return $this->hasMany(FeeAgreement::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [CaseStatus::CLOSED->value, CaseStatus::ARCHIVED->value]);
    }

    public function getFullNumberAttribute(): string
    {
        if (! $this->case_number) {
            return $this->reference;
        }

        return $this->case_year ? "{$this->case_number}/{$this->case_year}" : $this->case_number;
    }

    public function getDisplayTitleAttribute(): string
    {
        return "{$this->full_number} - {$this->title}";
    }
}
