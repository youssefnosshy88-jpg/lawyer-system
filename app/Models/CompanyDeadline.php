<?php

namespace App\Models;

use App\Enums\DeadlineStatus;
use App\Enums\DeadlineType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDeadline extends Model
{
    protected $fillable = [
        'company_id', 'type', 'title', 'due_at', 'status', 'recurring_yearly', 'reminder_sent', 'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => DeadlineType::class,
            'status' => DeadlineStatus::class,
            'due_at' => 'date',
            'completed_at' => 'date',
            'recurring_yearly' => 'boolean',
            'reminder_sent' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', '!=', DeadlineStatus::DONE->value);
    }

    public function markDone(): void
    {
        $this->update(['status' => DeadlineStatus::DONE, 'completed_at' => now()]);

        if ($this->recurring_yearly) {
            $this->replicate(['status', 'completed_at', 'reminder_sent'])
                ->fill(['due_at' => $this->due_at->addYear(), 'status' => DeadlineStatus::PENDING, 'reminder_sent' => false, 'completed_at' => null])
                ->save();
        }
    }
}
