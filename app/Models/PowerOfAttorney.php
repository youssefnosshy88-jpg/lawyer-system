<?php

namespace App\Models;

use App\Enums\PoaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowerOfAttorney extends Model
{
    protected $table = 'powers_of_attorney';

    protected $fillable = [
        'client_id', 'number', 'notary_office', 'issued_at', 'expires_at', 'status',
        'lawyer_ids', 'scope', 'file_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'status' => PoaStatus::class,
            'lawyer_ids' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
