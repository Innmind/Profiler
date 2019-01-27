<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway;

use Innmind\Profiler\Web\Gateway\Environment;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
};
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new Environment(
            $creator = $this->createMock(ResourceCreator::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
    }
}
