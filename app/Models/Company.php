<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Enums\LegalForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'name', 'name_en', 'legal_form', 'status', 'authorized_capital', 'issued_capital', 'paid_capital',
        'currency', 'commercial_register_no', 'commercial_register_office', 'commercial_register_expires_at',
        'tax_card_no', 'tax_office', 'gafi_file_no', 'gafi_license_no', 'incorporated_at', 'law', 'activity',
        'address', 'governorate', 'fiscal_year_end_month', 'responsible_lawyer_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'legal_form' => LegalForm::class,
            'status' => CompanyStatus::class,
            'authorized_capital' => 'decimal:2',
            'issued_capital' => 'decimal:2',
            'paid_capital' => 'decimal:2',
            'commercial_register_expires_at' => 'date',
            'incorporated_at' => 'date',
            'fiscal_year_end_month' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function responsibleLawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_lawyer_id');
    }

    public function partners(): HasMany
    {
        return $this->hasMany(CompanyPartner::class);
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(CompanyProcedure::class)->latest();
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(CompanyDeadline::class)->orderBy('due_at');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'en' && $this->name_en ? $this->name_en : $this->name;
    }
}
