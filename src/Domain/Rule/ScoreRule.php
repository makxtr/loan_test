<?php

declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Model\Client;

class ScoreRule implements RuleInterface
{
    public function validate(Client $client): bool
    {
        return $client->getScore() > 500;
    }
}
