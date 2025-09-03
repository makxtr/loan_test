<?php

declare(strict_types=1);

namespace App\Domain\Enum;

trait HasValuesTrait
{
    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}