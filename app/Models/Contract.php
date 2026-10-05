<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use GeneratesReference, SoftDeletes;

    protected string $referencePrefix = 'CT';

    protected $fillable = [
        'reference', 'client_id', 'legal_case_id', 'company_id', 'title', 'type', 'second_party', 'status',
        'signed_at', 'starts_at', 'ends_at', 'value', 'file_path', 'drafted_by', 'summary', 'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContractStatus::class,
            'signed_at' => 'date',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'value' => 'decimal:2',
            'visible_to_client' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function drafter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'drafted_by');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
