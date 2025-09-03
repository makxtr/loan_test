<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum CreditStatusEnum: string
{
    use HasValuesTrait;

    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
}
