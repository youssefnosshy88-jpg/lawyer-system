<?php

namespace App\Models;

use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Opponent extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'national_id', 'phone', 'address', 'lawyer_name', 'lawyer_phone', 'notes'];

    protected function casts(): array
    {
        return ['type' => ClientType::class];
    }

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(LegalCase::class, 'case_opponent')->withPivot('role');
    }
}
