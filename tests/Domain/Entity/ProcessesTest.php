<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Processes,
    Entity\Section,
    Entity\Section\Identity,
};
use Innmind\Immutable\Stream;
use PHPUnit\Framework\TestCase;

class ProcessesTest extends TestCase
{
    public function testInterface()
    {
        $section = new Processes(
            $identity = Identity::generate('app-graph'),
            $processes = Stream::of('string', 'cat foo', 'echo bar')
        );

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertSame($processes, $section->processes());
    }
}
