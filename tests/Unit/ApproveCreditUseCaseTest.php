<?php

declare(strict_types=1);

namespace Unit;

use App\Application\DTO\ApproveCreditDTO;
use App\Application\DTO\ClientDTO;
use App\Application\Rule\Modificator;
use App\Application\Rule\RuleChecker;
use App\Application\UseCase\ApproveCreditUseCase;
use App\Application\UseCase\CreateClientUseCase;
use App\Domain\Enum\CreditStatusEnum;
use App\Domain\Enum\RegionEnum;
use App\Domain\Factory\ClientFactory;
use App\Domain\Factory\CreditFactory;
use App\Domain\Modificator\OstravaRateIncrease;
use App\Domain\Rule\IncomeRule;
use App\Domain\Rule\ScoreRule;
use App\Infrastructure\Repository\JsonClientRepository;
use App\Infrastructure\Repository\JsonCreditRepository;
use App\Infrastructure\Service\NotificationService;
use PHPUnit\Framework\TestCase;

class ApproveCreditUseCaseTest extends TestCase
{
    private ApproveCreditUseCase $useCase;
    private JsonClientRepository $clientRepository;
    private JsonCreditRepository $creditRepository;
    private ClientFactory $clientFactory;
    private string $clientStorageFile;
    private string $creditStorageFile;
    private NotificationService $notificationService;

    protected function setUp(): void
    {
        $this->clientStorageFile = sys_get_temp_dir() . '/clients_test_' . uniqid() . '.json';
        $this->creditStorageFile = sys_get_temp_dir() . '/credits_test_' . uniqid() . '.json';
        $this->clientFactory = new ClientFactory();
        $this->clientRepository = new JsonClientRepository($this->clientStorageFile, $this->clientFactory);
        $this->creditRepository = new JsonCreditRepository($this->creditStorageFile, new CreditFactory());
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);

        $rules = [
            new ScoreRule(),
            new IncomeRule()
        ];
        $ruleChecker = new RuleChecker($rules);

        $this->notificationService = $this->createMock(NotificationService::class);

        $modificator = new Modificator([new OstravaRateIncrease()]);

        $this->useCase = new ApproveCreditUseCase(
            $this->clientRepository,
            $this->creditRepository,
            new CreditFactory(),
            $this->notificationService,
            $modificator,
            $ruleChecker
        );

        $clientDTO = new ClientDTO(
            name: 'Petr Pavel',
            age: 35,
            region: RegionEnum::PR->value,
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
        if (file_exists($this->clientStorageFile)) {
            unlink($this->clientStorageFile);
        }
        if (file_exists($this->creditStorageFile)) {
            unlink($this->creditStorageFile);
        }
    }

    public function testExecuteSuccess(): void
    {
        $startDate = (new \DateTimeImmutable())->format(\DateTime::ATOM);
        $endDate = (new \DateTimeImmutable())->modify('+1 year')->format(\DateTime::ATOM);

        $dto = new ApproveCreditDTO(
            pin: '123-45-6789',
            amount: 2000,
            startDate: $startDate,
            endDate: $endDate
        );

        $this->notificationService->expects($this->once())
            ->method('notifyClient')
            ->with($this->anything(), CreditStatusEnum::APPROVED->value);

        $result = $this->useCase->execute($dto);

        $this->assertTrue($result['success']);
        $this->assertEquals('Credit approved successfully', $result['message']);
        $this->assertNotNull($result['creditId']);

        $credit = $this->creditRepository->findById($result['creditId']);
        $this->assertNotNull($credit);
        $this->assertEquals('123-45-6789', $credit->getClientPin());
        $this->assertEquals(2000.0, $credit->getAmount());
        $this->assertEquals(10, $credit->getRate());
        $this->assertEquals($startDate, $credit->getStartDate()->format(\DateTime::ATOM));
        $this->assertEquals($endDate, $credit->getEndDate()->format(\DateTime::ATOM));
    }

    public function testExecuteSuccessWithOstravaRateIncrease(): void
    {
        $clientDTO = new ClientDTO(
            name: 'Jan Novak',
            age: 40,
            region: RegionEnum::OS->value,
            income: 2000.0,
            score: 600,
            pin: '987-65-4321',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);
        $createClientUseCase->execute($clientDTO);

        $startDate = (new \DateTimeImmutable())->format(\DateTime::ATOM);
        $endDate = (new \DateTimeImmutable())->modify('+1 year')->format(\DateTime::ATOM);

        $dto = new ApproveCreditDTO(
            pin: '987-65-4321',
            amount: 2000,
            startDate: $startDate,
            endDate: $endDate
        );

        $this->notificationService->expects($this->once())
            ->method('notifyClient')
            ->with($this->anything(), CreditStatusEnum::APPROVED->value);

        $result = $this->useCase->execute($dto);

        $this->assertTrue($result['success']);
        $this->assertEquals('Credit approved successfully', $result['message']);
        $this->assertNotNull($result['creditId']);

        $credit = $this->creditRepository->findById($result['creditId']);
        $this->assertNotNull($credit);
        $this->assertEquals('987-65-4321', $credit->getClientPin());
        $this->assertEquals(2000.0, $credit->getAmount());
        $this->assertEquals(15, $credit->getRate()); // check modificator
        $this->assertEquals($startDate, $credit->getStartDate()->format(\DateTime::ATOM));
        $this->assertEquals($endDate, $credit->getEndDate()->format(\DateTime::ATOM));
    }

    public function testExecuteFailsOnNonExistentClient(): void
    {
        $startDate = (new \DateTimeImmutable())->format(\DateTime::ATOM);
        $endDate = (new \DateTimeImmutable())->modify('+1 year')->format(\DateTime::ATOM);

        $dto = new ApproveCreditDTO(
            pin: '999-99-9999',
            amount: 2000,
            startDate: $startDate,
            endDate: $endDate
        );

        $this->notificationService->expects($this->never())
            ->method('notifyClient');

        $result = $this->useCase->execute($dto);

        $this->assertFalse($result['success']);
        $this->assertEquals('Client with PIN 999-99-9999 not found', $result['message']);
        $this->assertNull($result['creditId']);
    }

    public function testExecuteFailsOnLowScore(): void
    {
        $clientDTO = new ClientDTO(
            name: 'Jan Novak',
            age: 40,
            region: RegionEnum::OS->value,
            income: 2000.0,
            score: 500,
            pin: '987-65-4321',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);
        $createClientUseCase->execute($clientDTO);

        $startDate = (new \DateTimeImmutable())->format(\DateTime::ATOM);
        $endDate = (new \DateTimeImmutable())->modify('+1 year')->format(\DateTime::ATOM);

        $dto = new ApproveCreditDTO(
            pin: '987-65-4321',
            amount: 2000,
            startDate: $startDate,
            endDate: $endDate
        );

        $this->notificationService->expects($this->once())
            ->method('notifyClient')
            ->with($this->anything(), CreditStatusEnum::REJECTED->value);

        $result = $this->useCase->execute($dto);

        $this->assertFalse($result['success']);
        $this->assertEquals('Rejected', $result['message']);
        $this->assertNull($result['creditId']);
    }

    public function testExecuteFailsOnLowIncome(): void
    {
        $clientDTO = new ClientDTO(
            name: 'Jan Novak',
            age: 40,
            region: RegionEnum::OS->value,
            income: 500.0,
            score: 600,
            pin: '987-65-4321',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );
        $createClientUseCase = new CreateClientUseCase($this->clientRepository, $this->clientFactory);
        $createClientUseCase->execute($clientDTO);

        $startDate = (new \DateTimeImmutable())->format(\DateTime::ATOM);
        $endDate = (new \DateTimeImmutable())->modify('+1 year')->format(\DateTime::ATOM);

        $dto = new ApproveCreditDTO(
            pin: '987-65-4321',
            amount: 2001,
            startDate: $startDate,
            endDate: $endDate
        );

        $this->notificationService->expects($this->once())
            ->method('notifyClient')
            ->with($this->anything(), CreditStatusEnum::REJECTED->value);

        $result = $this->useCase->execute($dto);

        $this->assertFalse($result['success']);
        $this->assertEquals('Rejected', $result['message']);
        $this->assertNull($result['creditId']);
    }
}
