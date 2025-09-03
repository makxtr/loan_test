<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Application\DTO\ApproveCreditDTO;
use App\Domain\Model\Credit;
use Symfony\Component\Uid\Uuid;

class CreditFactory
{
    public function createCredit(ApproveCreditDTO $dto): Credit
    {
        return new Credit(
            id: Uuid::v4()->toRfc4122(),
            name: 'Credit for ' . $dto->pin,
            amount: $dto->amount,
            startDate: \DateTimeImmutable::createFromFormat(\DateTime::ATOM, $dto->startDate),
            endDate: \DateTimeImmutable::createFromFormat(\DateTime::ATOM, $dto->endDate),
            clientPin: $dto->pin
        );
    }
}
