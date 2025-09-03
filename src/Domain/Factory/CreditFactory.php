<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Application\DTO\ApproveCreditDTO;
use App\Application\DTO\CreditDTO;
use App\Domain\Model\Credit;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

class CreditFactory
{
    public function createApprovedCredit(ApproveCreditDTO $dto): Credit
    {
        return new Credit(
            id: Uuid::v4()->toRfc4122(),
            name: 'Credit for ' . $dto->pin,
            amount: $dto->amount,
            startDate: \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dto->startDate),
            endDate: \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dto->endDate),
            clientPin: $dto->pin
        );
    }

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

    public function createDTOFromArray(array $data): CreditDTO
    {
        return new CreditDTO(
            $data['id'],
            $data['name'],
            $data['amount'],
            $data['rate'],
            $data['startDate'],
            $data['endDate'],
            $data['clientPin']
        );
    }
}
