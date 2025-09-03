<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum RegionEnum: string
{
    use HasValuesTrait;

    case PR = 'PR';
    case BR = 'BR';
    case OS = 'OS';
}
