<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\ApproveCreditDTO;
use App\Domain\Enum\CreditStatusEnum;
use App\Domain\Model\Credit;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\Repository\CreditRepositoryInterface;
use App\Domain\Service\Modificator;
use App\Domain\Service\RuleChecker;
use App\Infrastructure\Service\NotificationService;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

readonly class ApproveCreditUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private CreditRepositoryInterface $creditRepository,
        private NotificationService $notificationService,
        private Modificator $modificator,
        private RuleChecker $ruleChecker,
    ) {}

    public function execute(ApproveCreditDTO $dto): array
    {
        $client = $this->clientRepository->findByPin($dto->pin);
        if (!$client) {
            return [
                'success' => false,
                'message' => 'Client with PIN ' . $dto->pin . ' not found'
            ];
        }

        $checkResult = $this->ruleChecker->check($client);
        if (!$checkResult) {
            $this->notificationService->notifyClient($client, CreditStatusEnum::REJECTED->value);
            return [
                'success' => false,
                'message' => 'Rejected'
            ];
        }

        $credit = new Credit(
            id: Uuid::v4()->toRfc4122(),
            name: 'Credit for ' . $dto->pin,
            amount: $dto->amount,
            startDate: \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dto->startDate),
            endDate: \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dto->endDate),
            clientPin: $dto->pin
        );

        $this->modificator->apply($client, $credit);

        $this->creditRepository->add($credit);
        $this->notificationService->notifyClient($client, CreditStatusEnum::APPROVED->value);
        return [
            'success' => true,
            'message' => 'Credit approved successfully',
            'creditId' => $credit->getId()
        ];
    }
}
