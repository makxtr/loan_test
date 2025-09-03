<?php

declare(strict_types=1);

namespace App\UI\API;

use App\Application\DTO\ApproveCreditDTO;
use App\Application\DTO\CheckCreditDTO;
use App\Application\UseCase\ApproveCreditUseCase;
use App\Application\UseCase\CheckCreditUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class CreditController
{
    public function __construct(
        private readonly CheckCreditUseCase $checkCreditUseCase,
        private readonly ApproveCreditUseCase $approveCreditUseCase,
    )
    {
    }

    #[Route('/credit/check', name: 'api_check_credit', methods: ['POST'])]
    public function checkCredit(
        #[MapRequestPayload] CheckCreditDTO $checkCreditDTO
    ): JsonResponse
    {
        $result = $this->checkCreditUseCase->execute($checkCreditDTO);
        return new JsonResponse($result);
    }

    #[Route('/credit', name: 'api_credit_approved', methods: ['POST'])]
    public function approveCredit(
        #[MapRequestPayload] ApproveCreditDTO $approveCreditDTO
    ): JsonResponse
    {
        $result = $this->approveCreditUseCase->execute($approveCreditDTO);
        return new JsonResponse($result, $result['success'] ? 201 : 400);
    }
}
