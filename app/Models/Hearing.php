<?php

namespace App\Models;

use App\Enums\HearingStatus;
use App\Enums\HearingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hearing extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_case_id', 'scheduled_at', 'type', 'status', 'courtroom', 'lawyer_id', 'requirements',
        'outcome', 'decision', 'next_hearing_at', 'reminder_sent', 'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'next_hearing_at' => 'date',
            'type' => HearingType::class,
            'status' => HearingStatus::class,
            'reminder_sent' => 'boolean',
            'visible_to_client' => 'boolean',
        ];
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', HearingStatus::SCHEDULED)->where('scheduled_at', '>=', now()->startOfDay());
    }

    public function scopeBetween(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('scheduled_at', [$from, $to]);
    }
}
