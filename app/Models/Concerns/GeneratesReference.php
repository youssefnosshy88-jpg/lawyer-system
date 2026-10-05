<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait GeneratesReference
{
    public static function bootGeneratesReference(): void
    {
        static::creating(function (Model $model): void {
            $column = $model->referenceColumn();

            if (blank($model->{$column})) {
                $model->{$column} = $model->nextReference();
            }
        });
    }

    public function referenceColumn(): string
    {
        return property_exists($this, 'referenceColumn') ? $this->referenceColumn : 'reference';
    }

    public function referencePrefix(): string
    {
        return property_exists($this, 'referencePrefix') ? $this->referencePrefix : strtoupper(substr(class_basename($this), 0, 3));
    }

    public function nextReference(): string
    {
        $year = now()->format('Y');
        $prefix = "{$this->referencePrefix()}-{$year}-";

        $last = static::withoutGlobalScopes()
            ->where($this->referenceColumn(), 'like', "{$prefix}%")
            ->orderByDesc($this->referenceColumn())
            ->value($this->referenceColumn());

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
