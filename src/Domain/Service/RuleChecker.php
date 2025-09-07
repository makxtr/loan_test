<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Client;
use App\Domain\Rule\RuleInterface;

class RuleChecker
{
    /** @var RuleInterface[] */
    private iterable $rules;

    public function __construct(iterable $rules)
    {
        $this->rules = $rules;
    }

    public function check(Client $client): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->validate($client)) {
                return false;
            }
        }
        return true;
    }
}
