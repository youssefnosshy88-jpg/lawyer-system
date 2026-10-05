<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_en', 'color', 'is_active'];

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
