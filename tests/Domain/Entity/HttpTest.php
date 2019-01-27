<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\Entity\{
    Http,
    Section,
    Section\Identity,
};
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    public function testReceived()
    {
        $section = Http::received(
            $identity = Identity::generate(Http::class),
            'some request'
        );

        $this->assertInstanceOf(Http::class, $section);
        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertSame('some request', $section->request());
        $this->assertFalse($section->hasRespondedYet());
    }

    public function testRespondedWith()
    {
        $section = Http::received(
            Identity::generate(Http::class),
            'some request'
        );

        $this->assertNull($section->respondedWith('some response'));
        $this->assertTrue($section->hasRespondedYet());
        $this->assertSame('some response', $section->response());
    }
}
