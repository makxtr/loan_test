<?php

declare(strict_types=1);

namespace App\Application\DTO;

class CreditDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public int $amount,
        public int $rate,
        public string $startDate,
        public string $endDate,
        public string $clientPin,
    ) {
    }
}
