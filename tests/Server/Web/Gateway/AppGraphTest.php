<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway;

use Innmind\Profiler\Server\Web\Gateway\AppGraph;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
};
use PHPUnit\Framework\TestCase;

class AppGraphTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new AppGraph(
            $creator = $this->createMock(ResourceCreator::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
    }
}
