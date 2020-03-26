<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity\Profile;

use Innmind\Profiler\Domain\Entity\Profile\Identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    public function testInterface()
    {
        $identity = Identity::generate();

        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertNotEmpty($identity->toString());
        $this->assertNotSame($identity->toString(), Identity::generate()->toString());
    }
}
