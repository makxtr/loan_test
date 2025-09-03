<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Factory\ClientFactory;
use App\Domain\Model\Client;
use App\Domain\Repository\ClientRepositoryInterface;

class JsonClientRepository implements ClientRepositoryInterface
{
    private array $clients = [];
    private string $storageFile;

    public function __construct(
        string $storageFile,
        private readonly ClientFactory $clientFactory
    ) {
        $this->storageFile = $storageFile;
        $this->loadClients();
    }

    public function add(Client $client): void
    {
        $this->clients[$client->getPin()] = $client;
        $this->saveClients();
    }

    public function findByPin(string $pin): ?Client
    {
        return $this->clients[$pin] ?? null;
    }

    private function loadClients(): void
    {
        if (file_exists($this->storageFile)) {
            $data = json_decode(file_get_contents($this->storageFile), true);
            if (is_array($data)) {
                foreach ($data as $clientData) {
                    $dto = $this->clientFactory->createDTOFromArray($clientData);
                    $this->clients[$clientData['pin']] = $this->clientFactory->createFromDTO($dto);
                }
            }
        }
    }

    private function saveClients(): void
    {
        $data = [];
        /** @var Client $client */
        foreach ($this->clients as $client) {
            $data[] = [
                'name' => $client->getName(),
                'age' => $client->getAge(),
                'region' => $client->getRegion(),
                'income' => $client->getIncome(),
                'score' => $client->getScore(),
                'pin' => $client->getPin(),
                'email' => $client->getEmail(),
                'phone' => $client->getPhone(),
            ];
        }
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
