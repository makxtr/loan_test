<?php

declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Model\Client;

interface RuleInterface
{
    public function validate(Client $client): bool;
}
