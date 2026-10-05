<?php

namespace App\Models;

use App\Enums\ProcedureStatus;
use App\Enums\ProcedureType;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CompanyProcedure extends Model
{
    use GeneratesReference;

    protected string $referencePrefix = 'PR';

    protected $fillable = [
        'reference', 'company_id', 'type', 'status', 'authority', 'authority_reference', 'assigned_to', 'started_at',
        'submitted_at', 'due_at', 'completed_at', 'government_fees', 'service_fees', 'checklist', 'description',
        'result', 'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProcedureType::class,
            'status' => ProcedureStatus::class,
            'started_at' => 'date',
            'submitted_at' => 'date',
            'due_at' => 'date',
            'completed_at' => 'date',
            'government_fees' => 'decimal:2',
            'service_fees' => 'decimal:2',
            'checklist' => 'array',
            'visible_to_client' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [ProcedureStatus::COMPLETED->value, ProcedureStatus::REJECTED->value]);
    }

    public function getChecklistProgressAttribute(): int
    {
        $items = collect($this->checklist ?? []);

        if ($items->isEmpty()) {
            return 0;
        }

        return (int) round($items->where('done', true)->count() / $items->count() * 100);
    }
}
