<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\CallGraph,
    Entity\Section,
    Entity\Section\Identity,
    Model\Json,
};
use PHPUnit\Framework\TestCase;

class CallGraphTest extends TestCase
{
    public function testInterface()
    {
        $section = new CallGraph(
            $identity = Identity::generate('app-graph'),
            $json = new Json('{}')
        );

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertSame($json, $section->graph());
    }
}
