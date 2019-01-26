<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway;

use Innmind\Profiler\Server\Web\Gateway\Exception;
use Innmind\Rest\Server\{
    Gateway,
    ResourceCreator,
    ResourceLinker,
};
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testInterface()
    {
        $gateway = new Exception(
            $creator = $this->createMock(ResourceCreator::class),
            $linker = $this->createMock(ResourceLinker::class)
        );

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame($creator, $gateway->resourceCreator());
        $this->assertSame($linker, $gateway->resourceLinker());
    }
}
