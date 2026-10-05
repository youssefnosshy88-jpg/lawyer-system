<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_en', 'type', 'city', 'address', 'phone', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'en' && $this->name_en ? $this->name_en : $this->name;
    }
}
