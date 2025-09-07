<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Credit;
use DateTimeInterface;

class CreditFactory
{
    public function createCreditFromArray(array $data): Credit
    {
        return new Credit(
            id: $data['id'],
            name: $data['name'],
            amount: $data['amount'],
            startDate: \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $data['startDate']),
            endDate: \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $data['endDate']),
            rate: $data['rate'],
            clientPin: $data['clientPin']
        );
    }
}
