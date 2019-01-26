<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway;

use Innmind\Profiler\Server\Web\Gateway\RequestResponse;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
    ResourceUpdater,
    ResourceLinker,
};
use PHPUnit\Framework\TestCase;

class RequestResponseTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new RequestResponse(
            $creator = $this->createMock(ResourceCreator::class),
            $updater = $this->createMock(ResourceUpdater::class),
            $linker = $this->createMock(ResourceLinker::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
        $this->assertSame($updater, $gateway->resourceUpdater());
        $this->assertSame($linker, $gateway->resourceLinker());
    }
}
