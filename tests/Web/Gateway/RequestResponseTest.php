<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway;

use Innmind\Profiler\Web\Gateway\RequestResponse;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
    ResourceUpdater,
};
use PHPUnit\Framework\TestCase;

class RequestResponseTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new RequestResponse(
            $creator = $this->createMock(ResourceCreator::class),
            $updater = $this->createMock(ResourceUpdater::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
        $this->assertSame($updater, $gateway->resourceUpdater());
    }
}
