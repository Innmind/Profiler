<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Environment,
    Entity\Section,
    Entity\Section\Identity,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testInterface()
    {
        $section = new Environment(
            $identity = Identity::generate('app-graph'),
            $pairs = Set::of('string', 'FOO=bar', 'BAR=42')
        );

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertSame($pairs, $section->pairs());
        $this->assertSame("FOO=bar\nBAR=42", (string) $section);
    }
}
