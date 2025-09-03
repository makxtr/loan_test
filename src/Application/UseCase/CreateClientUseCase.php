<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\ClientDTO;
use App\Domain\Factory\ClientFactory;
use App\Domain\Repository\ClientRepositoryInterface;

readonly class CreateClientUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private ClientFactory $clientFactory
    ) {}

    public function execute(ClientDTO $clientDTO): array
    {
        try {
            $client = $this->clientFactory->createFromDTO($clientDTO);
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
