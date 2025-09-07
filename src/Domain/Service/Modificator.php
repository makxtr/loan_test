<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Client;
use App\Domain\Model\Credit;
use App\Domain\Modificator\ModificatorInterface;

class Modificator
{
    /** @var ModificatorInterface[] */
    private iterable $modificators;

    public function __construct(iterable $modificators)
    {
        $this->modificators = $modificators;
    }

    public function apply(Client $client, Credit $credit): void
    {
        foreach ($this->modificators as $modificator) {
            $modificator->modify($client, $credit);
        }
    }
}
