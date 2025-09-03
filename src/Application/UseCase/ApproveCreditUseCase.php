<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\ApproveCreditDTO;
use App\Application\Rule\Modificator;
use App\Application\Rule\RuleChecker;
use App\Domain\Factory\CreditFactory;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\Repository\CreditRepositoryInterface;
use App\Infrastructure\Service\NotificationService;

readonly class ApproveCreditUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private CreditRepositoryInterface $creditRepository,
        private CreditFactory $creditFactory,
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
            $this->notificationService->notifyClient($client, 'rejected');
            return [
                'success' => false,
                'message' => 'Rejected'
            ];
        }

        $credit = $this->creditFactory->createCredit($dto);

        $this->modificator->apply($client, $credit);

        $this->creditRepository->add($credit);
        $this->notificationService->notifyClient($client, 'approved');
        return [
            'success' => true,
            'message' => 'Credit approved successfully',
            'creditId' => $credit->getId()
        ];
    }
}
