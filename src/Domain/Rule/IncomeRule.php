<?php

declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Model\Client;

class IncomeRule implements RuleInterface
{
    public function validate(Client $client): bool
    {
        return $client->getIncome() >= 1000;
    }
}
