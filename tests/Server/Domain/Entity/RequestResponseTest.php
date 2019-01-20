<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\Entity\{
    RequestResponse,
    Section\Identity,
};
use PHPUnit\Framework\TestCase;

class RequestResponseTest extends TestCase
{
    public function testReceived()
    {
        $section = RequestResponse::received(
            $identity = $this->createMock(Identity::class),
            'some request'
        );

        $this->assertInstanceOf(RequestResponse::class, $section);
        $this->assertSame($identity, $section->identity());
        $this->assertSame('some request', $section->request());
        $this->assertFalse($section->hasRespondedYet());
    }

    public function testRespondedWith()
    {
        $section = RequestResponse::received(
            $this->createMock(Identity::class),
            'some request'
        );

        $this->assertNull($section->respondedWith('some response'));
        $this->assertTrue($section->hasRespondedYet());
        $this->assertSame('some response', $section->response());
    }
}
