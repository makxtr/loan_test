<?php

declare(strict_types=1);

namespace App\Domain\Modificator;

use App\Domain\Model\Client;
use App\Domain\Model\Credit;

interface ModificatorInterface
{
    public function modify(Client $client, Credit $credit): void;
}
