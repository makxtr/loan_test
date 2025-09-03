<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ClientDTO
{
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Type("integer")]
    #[Assert\Positive]
    public int $age;

    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $region;

    #[Assert\NotBlank]
    #[Assert\Type("numeric")]
    #[Assert\Positive]
    public float $income;

    #[Assert\NotBlank]
    #[Assert\Type("integer")]
    #[Assert\Positive]
    public int $score;

    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $pin;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $phone;

    public function __construct(
        string $name,
        int $age,
        string $region,
        float $income,
        int $score,
        string $pin,
        string $email,
        string $phone
    ) {
        $this->name = $name;
        $this->age = $age;
        $this->region = $region;
        $this->income = $income;
        $this->score = $score;
        $this->pin = $pin;
        $this->email = $email;
        $this->phone = $phone;
    }
}
