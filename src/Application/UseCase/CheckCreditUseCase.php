<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\CheckCreditDTO;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\Service\RuleChecker;

readonly class CheckCreditUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private RuleChecker $ruleChecker
    ) {}

    public function execute(CheckCreditDTO $checkCreditDTO): bool
    {
        $client = $this->clientRepository->findByPin($checkCreditDTO->pin);
        if (!$client) return false;

        return $this->ruleChecker->check($client);
    }
}
