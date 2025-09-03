<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Client;

interface ClientRepositoryInterface
{
    public function add(Client $client): void;
    public function findByPin(string $pin): ?Client;
}
