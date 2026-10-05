<?php

namespace App\Models;

use App\Enums\PartnerRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyPartner extends Model
{
    protected $fillable = [
        'company_id', 'name', 'nationality', 'id_type', 'id_number', 'role', 'share_percentage', 'shares_count',
        'share_value', 'is_signatory', 'phone', 'email', 'address', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'role' => PartnerRole::class,
            'share_percentage' => 'decimal:2',
            'share_value' => 'decimal:2',
            'shares_count' => 'integer',
            'is_signatory' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
