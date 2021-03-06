<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Processes,
    Entity\Section,
    Entity\Section\Identity,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class ProcessesTest extends TestCase
{
    public function testInterface()
    {
        $section = new Processes(
            $identity = Identity::generate('app-graph'),
            $processes = Set::of('string', 'cat foo', 'echo bar')
        );

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertSame(['cat foo', 'echo bar'], $section->processes());
    }
}
