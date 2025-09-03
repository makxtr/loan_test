<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Model\Client;
use Psr\Log\LoggerInterface;

readonly class NotificationService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function notifyClient(Client $client, string $status): void
    {
        $message = sprintf(
            '[%s] Notification to client %s: Credit %s.',
            date('Y-m-d H:i:s'),
            $client->getName(),
            $status
        );
        $this->logger->info($message);
    }
}
