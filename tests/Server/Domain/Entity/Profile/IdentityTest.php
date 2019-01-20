<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity\Profile;

use Innmind\Profiler\Server\Domain\Entity\Profile\Identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = Identity::generate();

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertNotEmpty((string) $identity);
        $this->assertNotSame((string) $identity, (string) Identity::generate());
    }
}
