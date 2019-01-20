<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity\Section;

use Innmind\Profiler\Server\Domain\Entity\Section\Identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = Identity::generate('foo');

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertSame('foo', $identity->section());
        $this->assertNotEmpty((string) $identity);
        $this->assertNotSame((string) $identity, (string) Identity::generate('foo'));
    }
}
