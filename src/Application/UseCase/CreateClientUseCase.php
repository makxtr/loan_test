<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\ClientDTO;
use App\Domain\Model\Client;
use App\Domain\Repository\ClientRepositoryInterface;

readonly class CreateClientUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(ClientDTO $dto): array
    {
        try {
            $client = new Client(
                name: $dto->name,
                age: $dto->age,
                region: $dto->region,
                income: $dto->income,
                score: $dto->score,
                pin: $dto->pin,
                email: $dto->email,
                phone: $dto->phone
            );

            $this->clientRepository->add($client);

            return [
                'success' => true,
                'message' => 'Client created successfully',
                'pin' => $client->getPin()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating client: ' . $e->getMessage(),
                'pin' => null
            ];
        }
    }
}
