<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway;

use Innmind\Profiler\Web\Gateway\Http;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
    ResourceUpdater,
};
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new Http(
            $creator = $this->createMock(ResourceCreator::class),
            $updater = $this->createMock(ResourceUpdater::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
        $this->assertSame($updater, $gateway->resourceUpdater());
    }
}
