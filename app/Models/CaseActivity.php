<?php

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseActivity extends Model
{
    protected $fillable = ['legal_case_id', 'user_id', 'type', 'title', 'body', 'occurred_at', 'hours_spent', 'visible_to_client'];

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'occurred_at' => 'datetime',
            'hours_spent' => 'decimal:2',
            'visible_to_client' => 'boolean',
        ];
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
