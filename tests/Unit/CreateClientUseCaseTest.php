<?php

declare(strict_types=1);

namespace Unit;

use App\Application\DTO\ClientDTO;
use App\Application\UseCase\CreateClientUseCase;
use App\Domain\Factory\ClientFactory;
use App\Infrastructure\Repository\JsonClientRepository;
use PHPUnit\Framework\TestCase;

class CreateClientUseCaseTest extends TestCase
{
    private CreateClientUseCase $useCase;
    private JsonClientRepository $clientRepository;
    private string $storageFile;

    protected function setUp(): void
    {
        $this->storageFile = sys_get_temp_dir() . '/clients_test.json';
        $clientFactory = new ClientFactory();
        $this->clientRepository = new JsonClientRepository($this->storageFile, $clientFactory);
        $this->useCase = new CreateClientUseCase($this->clientRepository, $clientFactory);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->storageFile)) {
            unlink($this->storageFile);
        }
    }

    public function testExecuteSuccessAndClientPersisted(): void
    {
        $clientDTO = new ClientDTO(
            name: 'Petr Pavel',
            age: 35,
            region: 'PR',
            income: 1500.0,
            score: 600,
            pin: '123-45-6789',
            email: 'petr.pavel@example.com',
            phone: '+420123456789'
        );

        $result = $this->useCase->execute($clientDTO);

        $this->assertTrue($result['success']);
        $this->assertEquals('Client created successfully', $result['message']);
        $this->assertEquals('123-45-6789', $result['pin']);

        $client = $this->clientRepository->findByPin('123-45-6789');
        $this->assertNotNull($client);
        $this->assertEquals('Petr Pavel', $client->getName());
        $this->assertEquals(35, $client->getAge());
        $this->assertEquals('PR', $client->getRegion());
        $this->assertEquals(1500.0, $client->getIncome());
        $this->assertEquals(600, $client->getScore());
        $this->assertEquals('123-45-6789', $client->getPin());
        $this->assertEquals('petr.pavel@example.com', $client->getEmail());
        $this->assertEquals('+420123456789', $client->getPhone());
    }
}
