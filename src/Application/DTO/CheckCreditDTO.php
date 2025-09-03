<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CheckCreditDTO
{
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $pin;

    #[Assert\NotBlank]
    #[Assert\Type("numeric")]
    #[Assert\Positive]
    public float $amount;

    public function __construct(
        string $pin,
        float $amount
    ) {
        $this->pin = $pin;
        $this->amount = $amount;
    }
}
