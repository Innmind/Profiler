<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
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
            $identity = Identity::generate('exception'),
            $svg = new Svg('<svg></svg>')
        );

        $this->assertSame($identity, $exception->identity());
        $this->assertSame($svg, $exception->graph());
    }
}
