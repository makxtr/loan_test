<?php

declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Model\Client;

class AgeRule implements RuleInterface
{
    public function validate(Client $client): bool
    {
        return $client->getAge() >= 18 && $client->getAge() <= 60;
    }
}
