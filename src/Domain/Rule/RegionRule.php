<?php

declare(strict_types=1);

namespace App\Domain\Rule;

use App\Domain\Enum\RegionEnum;
use App\Domain\Model\Client;

class RegionRule implements RuleInterface
{
    public function validate(Client $client): bool
    {
        return in_array($client->getRegion(), RegionEnum::values(), true);
    }
}
