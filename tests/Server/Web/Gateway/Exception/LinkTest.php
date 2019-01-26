<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway\Exception;

use Innmind\Profiler\Server\{
    Web\Gateway\Exception\Link,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile,
    Domain\Entity\Section,
    Domain\Entity\Exception,
};
use Innmind\Rest\Server\{
    ResourceLinker,
    Reference,
    Link as ResourceLink,
    Identity\Identity,
};
use Innmind\Filesystem\Adapter\MemoryAdapter;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceLinker::class,
            new Link(
                new ProfileRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $link = new Link(
            $repository = new ProfileRepository(
                new MemoryAdapter
            )
        );
        $directory = (require 'src/Server/Web/config/resources.php')($clock);
        $profile = Profile::start(
            Profile\Identity::generate(),
            'foo',
            $clock->now()
        );
        $repository->add($profile);

        $link(
            new Reference(
                $directory->child('section')->definition('exception'),
                new Identity((string) $expected = Section\Identity::generate(Exception::class))
            ),
            new ResourceLink(
                new Reference(
                    $directory->definition('profile'),
                    new Identity((string) $profile->identity())
                ),
                'section-of'
            )
        );

        $profile = $repository->get($profile->identity());
        $this->assertCount(1, $profile->sections());
        $this->assertSame((string) $expected, (string) $profile->sections()->current());
        $this->assertSame($expected->section(), $profile->sections()->current()->section());
    }
}
