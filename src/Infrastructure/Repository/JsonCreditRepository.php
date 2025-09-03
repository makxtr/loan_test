<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Credit;
use App\Domain\Repository\CreditRepositoryInterface;

class JsonCreditRepository implements CreditRepositoryInterface
{
    private array $credits = [];
    private string $storageFile;

    public function __construct(
        string $storageFile = 'var/credits.json'
    ) {
        $this->storageFile = $storageFile;
        $this->loadCredits();
    }

    public function add(Credit $credit): void
    {
        $this->credits[$credit->getId()] = $credit;
        $this->saveCredits();
    }

    public function findById(string $id): ?Credit
    {
        return $this->credits[$id] ?? null;
    }

    private function loadCredits(): void
    {
        if (file_exists($this->storageFile)) {
            $data = json_decode(file_get_contents($this->storageFile), true);
            if (is_array($data)) {
                foreach ($data as $creditData) {
                    $this->credits[$creditData['id']] = new Credit(
                        id: $creditData['id'],
                        name: $creditData['name'],
                        amount: $creditData['amount'],
                        rate: $creditData['rate'],
                        startDate: new \DateTimeImmutable($creditData['startDate']),
                        endDate: new \DateTimeImmutable($creditData['endDate']),
                        clientPin: $creditData['clientPin']
                    );
                }
            }
        }
    }

    private function saveCredits(): void
    {
        $data = [];
        /** @var Credit $credit */
        foreach ($this->credits as $credit) {
            $data[] = [
                'id' => $credit->getId(),
                'name' => $credit->getName(),
                'amount' => $credit->getAmount(),
                'rate' => $credit->getRate(),
                'startDate' => $credit->getStartDate()->format(\DateTime::ATOM),
                'endDate' => $credit->getEndDate()->format(\DateTime::ATOM),
                'clientPin' => $credit->getClientPin()
            ];
        }
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
