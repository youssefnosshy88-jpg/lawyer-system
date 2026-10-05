<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'taskable_type', 'taskable_id', 'assigned_to', 'created_by', 'status', 'priority',
        'due_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (Task $task) => $task->created_by ??= auth()->id());

        static::saving(function (Task $task): void {
            if ($task->status === TaskStatus::DONE && ! $task->completed_at) {
                $task->completed_at = now();
            }
        });
    }

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [TaskStatus::TODO->value, TaskStatus::IN_PROGRESS->value]);
    }
}
