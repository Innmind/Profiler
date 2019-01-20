<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\{
    Entity\AppGraph,
    Entity\Section\Identity,
    Model\Svg,
};
use PHPUnit\Framework\TestCase;

class AppGraphTest extends TestCase
{
    public function testInterface()
    {
        $section = new AppGraph(
            $identity = Identity::generate('app-graph'),
            $svg = new Svg('<svg></svg>')
        );

        $this->assertSame($identity, $section->identity());
        $this->assertSame($svg, $section->graph());
    }
}
