<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Client;

class ClientFactory
{
    public function createFromArray(array $data): Client
    {
        return new Client(
            name: $data['name'],
            age: (int) $data['age'],
            region: $data['region'],
            income: (float) $data['income'],
            score: (int) $data['score'],
            pin: $data['pin'],
            email: $data['email'],
            phone: $data['phone']
        );
    }
}
