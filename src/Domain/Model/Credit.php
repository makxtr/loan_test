<?php

declare(strict_types=1);

namespace App\Domain\Model;

class Credit
{
    public function __construct(
        private string $id,
        private string $name,
        private int $amount,
        private \DateTimeInterface $startDate,
        private \DateTimeInterface $endDate,
        private int $rate = 10,
        private ?string $clientPin = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getRate(): int
    {
        return $this->rate;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function getClientPin(): ?string
    {
        return $this->clientPin;
    }

    public function increaseRate(int $delta): void
    {
        $this->rate += $delta;
    }
}
