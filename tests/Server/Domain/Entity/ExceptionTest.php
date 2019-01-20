<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\{
    Entity\Exception,
    Entity\Section\Identity,
    Model\Svg,
};
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testInterface()
    {
        $exception = new Exception(
            $identity = $this->createMock(Identity::class),
            $svg = new Svg('<svg></svg>')
        );

        $this->assertSame($identity, $exception->identity());
        $this->assertSame($svg, $exception->graph());
    }
}
