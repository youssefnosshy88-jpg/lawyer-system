<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Str;

trait TranslatableEnum
{
    public function getLabel(): string
    {
        return __('enums.'.static::translationKey().'.'.$this->value);
    }

    public static function translationKey(): string
    {
        return Str::snake(class_basename(static::class));
    }

    public static function options(): array
    {
        return collect(static::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->getLabel()])
            ->all();
    }

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
