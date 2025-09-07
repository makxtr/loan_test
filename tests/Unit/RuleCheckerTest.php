<?php

declare(strict_types=1);

namespace Unit;

use App\Domain\Model\Client;
use App\Domain\Rule\AgeRule;
use App\Domain\Rule\IncomeRule;
use App\Domain\Rule\PragueRejectRule;
use App\Domain\Rule\RegionRule;
use App\Domain\Rule\ScoreRule;
use App\Domain\Service\RuleChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RuleCheckerTest extends TestCase
{
    private Client $client;
    private MockObject $pragueRandomRejectRule;

    protected function setUp(): void
    {
        $this->client = new Client(
            name: 'Petr Pavel',
            age: 35,
            region: 'PR',
            income: 1500.0,
            score: 600,
            pin: '123-45-6789',
            email: 'petr.pavel@example.com',
            phone: '+420123456789'
        );

        $this->pragueRandomRejectRule = $this->createMock(PragueRejectRule::class);
    }

    private function createRuleChecker(): RuleChecker
    {
        return new RuleChecker([
            new ScoreRule(),
            new IncomeRule(),
            new AgeRule(),
            new RegionRule(),
            $this->pragueRandomRejectRule,
        ]);
    }

    public function testClientPassesAllRules(): void
    {
        $this->pragueRandomRejectRule
            ->method('validate')
            ->with($this->client)
            ->willReturn(true);

        $ruleChecker = $this->createRuleChecker();
        $result = $ruleChecker->check($this->client);

        $this->assertTrue($result);
    }

    public function testClientFailsLowScore(): void
    {
        $invalidClient = new Client(
            name: 'Jan Novak',
            age: 35,
            region: 'PR',
            income: 1500.0,
            score: 400,
            pin: '987-65-4321',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );

        $this->pragueRandomRejectRule
            ->method('validate')
            ->with($invalidClient)
            ->willReturn(true);

        $ruleChecker = $this->createRuleChecker();
        $result = $ruleChecker->check($invalidClient);

        $this->assertFalse($result);
    }

    public function testClientFailsInvalidRegion(): void
    {
        $invalidClient = new Client(
            name: 'Jan Novak',
            age: 35,
            region: 'XX',
            income: 1500.0,
            score: 600,
            pin: '987-65-4321',
            email: 'jan.novak@example.com',
            phone: '+420987654321'
        );

        $this->pragueRandomRejectRule
            ->expects($this->never())
            ->method('validate');

        $ruleChecker = $this->createRuleChecker();
        $result = $ruleChecker->check($invalidClient);

        $this->assertFalse($result);
    }

    public function testClientFailsPragueRandomReject(): void
    {
        $this->pragueRandomRejectRule
            ->method('validate')
            ->with($this->client)
            ->willReturn(false);

        $ruleChecker = $this->createRuleChecker();
        $result = $ruleChecker->check($this->client);

        $this->assertFalse($result);
    }
}
