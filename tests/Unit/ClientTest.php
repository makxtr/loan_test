<?php

declare(strict_types=1);

namespace Unit;

use App\Domain\Model\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClientSuccess()
    {
        $client = new Client(
            "Petr Pavel",
            35,
            "PR",
            1500,
            600,
            "123-45-6789",
            "petr.pavel@example.com",
            "+420123456789"
        );

        $this->assertEquals("Petr Pavel", $client->getName());
    }
}
