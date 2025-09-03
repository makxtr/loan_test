<?php

declare(strict_types=1);

namespace App\Domain\Modificator;

use App\Domain\Enum\RegionEnum;
use App\Domain\Model\Client;
use App\Domain\Model\Credit;

class OstravaRateIncrease implements ModificatorInterface
{
    public function modify(Client $client, Credit $credit): void
    {
        if ($client->getRegion() === RegionEnum::OS->value) {
            $credit->increaseRate(5);
        }
    }
}
