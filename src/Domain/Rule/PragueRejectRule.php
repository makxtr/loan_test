<?php

declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Enum\RegionEnum;
use App\Domain\Model\Client;

class PragueRejectRule implements RuleInterface
{
    public function validate(Client $client): bool
    {
        if (RegionEnum::PR->value !== $client->getRegion()) {
            return true;
        }
        return rand(1, 10) > 1;
    }
}
