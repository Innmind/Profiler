<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity\Remote;

use Innmind\Profiler\Domain\Entity\{
    Remote\Http,
    Remote\Http\Call,
    Section,
};
use Innmind\Immutable\Sequence;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    public function testInterface()
    {
        $section = new Http(
            $identity = Section\Identity::generate(Http::class)
        );

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertIsArray($section->calls());
        $this->assertCount(0, $section->calls());

        $this->assertNull($section->add($call = new Call('foo', 'bar')));
        $this->assertSame([$call], $section->calls());
    }
}
