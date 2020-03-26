<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity\Section;

use Innmind\Profiler\Domain\Entity\Section\Identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = Identity::generate('foo');

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertSame('foo', $identity->section());
        $this->assertNotEmpty($identity->toString());
        $this->assertNotSame($identity->toString(), Identity::generate('foo')->toString());
    }
}
