<?php

declare(strict_types=1);

namespace App\UI\API;

use App\Application\DTO\ClientDTO;
use App\Application\UseCase\CreateClientUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ClientController
{
    public function __construct(private readonly CreateClientUseCase $useCase)
    {
    }

    #[Route('client', name: 'api_create_client', methods: ['POST'])]
    public function createClient(
        #[MapRequestPayload] ClientDTO $clientDTO
    ): JsonResponse
    {
        $result = $this->useCase->execute($clientDTO);
        return new JsonResponse($result, $result['success'] ? 201 : 400);
    }
}
