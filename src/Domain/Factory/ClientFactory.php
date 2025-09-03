<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Application\DTO\ClientDTO;
use App\Domain\Model\Client;

class ClientFactory
{
    public function createFromDTO(ClientDTO $dto): Client
    {
        return new Client(
            name: $dto->name,
            age: $dto->age,
            region: $dto->region,
            income: $dto->income,
            score: $dto->score,
            pin: $dto->pin,
            email: $dto->email,
            phone: $dto->phone
        );
    }

    public function createDTOFromArray(array $data): ClientDTO
    {
        return new ClientDTO(
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
