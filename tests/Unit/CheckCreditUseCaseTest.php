<?php

declare(strict_types=1);

namespace Unit;

use App\Application\DTO\CheckCreditDTO;
use App\Application\DTO\ClientDTO;
use App\Application\Rule\RuleChecker;
use App\Application\UseCase\CheckCreditUseCase;
use App\Application\UseCase\CreateClientUseCase;
use App\Domain\Factory\ClientFactory;
use App\Domain\Rule\IncomeRule;
use App\Domain\Rule\ScoreRule;
use App\Infrastructure\Repository\JsonClientRepository;
use PHPUnit\Framework\TestCase;

class CheckCreditUseCaseTest extends TestCase
{
    private CheckCreditUseCase $useCase;
    private JsonClientRepository $clientRepository;
    private ClientFactory $clientFactory;
    private string $storageFile;

    protected function setUp(): void
    {
        $this->storageFile = sys_get_temp_dir() . '/clients_test.json';
        $this->clientFactory = new ClientFactory();
        $this->clientRepository = new JsonClientRepository($this->storageFile, $this->clientFactory);
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);

        $rules = [
            new ScoreRule(),
            new IncomeRule()
        ];
        $ruleChecker = new RuleChecker($rules);
        $this->useCase = new CheckCreditUseCase($this->clientRepository, $ruleChecker);

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
        $createClientUseCase->execute($clientDTO);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->storageFile)) {
            unlink($this->storageFile);
        }
    }

    public function testExecuteSuccess(): void
    {
        $checkCreditDTO = new CheckCreditDTO(
            pin: '123-45-6789',
            amount: 2000.0
        );

        $result = $this->useCase->execute($checkCreditDTO);

        $this->assertTrue($result);
    }

    public function testExecuteFailsOnNonExistentClient(): void
    {
        $checkCreditDTO = new CheckCreditDTO(
            pin: '999-99-9999',
            amount: 2000.0
        );

        $result = $this->useCase->execute($checkCreditDTO);

        $this->assertFalse($result);
    }

    public function testExecuteFailsOnLowScore(): void
    {
        $clientDTO = new ClientDTO(
            name: 'Jan Novak',
            age: 40,
            region: 'OS',
            income: 2000.0,
            score: 500,
            pin: '987-65-4321',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);
        $createClientUseCase->execute($clientDTO);

        $checkCreditDTO = new CheckCreditDTO(
            pin: '987-65-4321',
            amount: 2000.0
        );

        $result = $this->useCase->execute($checkCreditDTO);

        $this->assertFalse($result);
    }

    public function testExecuteFailsOnLowIncome(): void
    {
        $clientDTO = new ClientDTO(
            name: 'Jan Novak',
            age: 40,
            region: 'OS',
            income: 900.0,
            score: 500,
            pin: '987-65-4322',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);
        $createClientUseCase->execute($clientDTO);

        $checkCreditDTO = new CheckCreditDTO(
            pin: '987-65-4322',
            amount: 2000.0
        );

        $result = $this->useCase->execute($checkCreditDTO);

        $this->assertFalse($result);
    }
}
