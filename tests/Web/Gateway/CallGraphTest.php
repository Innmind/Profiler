<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway;

use Innmind\Profiler\Web\Gateway\CallGraph;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
};
use PHPUnit\Framework\TestCase;

class CallGraphTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new CallGraph(
            $creator = $this->createMock(ResourceCreator::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
    }
}
