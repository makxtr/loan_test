<?php

declare(strict_types=1);

namespace App\Application\DTO;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ApproveCreditDTO
{
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $pin;

    #[Assert\NotBlank]
    #[Assert\Type("numeric")]
    #[Assert\Positive]
    public int $amount;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public string $startDate;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public string $endDate;

    public function __construct(
        string $pin,
        int $amount,
        string $startDate,
        string $endDate
    ) {
        $this->pin = $pin;
        $this->amount = $amount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
