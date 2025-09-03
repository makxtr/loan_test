<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Credit;

interface CreditRepositoryInterface
{
    public function add(Credit $credit): void;
}
