<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway\Remote\Http;

use Innmind\Profiler\{
    Web\Gateway\Remote\Http\Create,
    Domain\Repository\SectionRepository,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile,
};
use Innmind\Rest\Server\{
    ResourceCreator,
    HttpResource\HttpResource,
    HttpResource\Property,
};
use Innmind\Filesystem\Adapter\InMemory;
use Innmind\TimeContinuum\Earth\Clock as Earth;
use function Innmind\Immutable\first;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceCreator::class,
            new Create(
                new SectionRepository(
                    new InMemory
                ),
                new ProfileRepository(
                    new InMemory
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new SectionRepository(
                $adapter = new InMemory
            ),
            $profiles = new ProfileRepository(
                new InMemory
            )
        );
        $profiles->add($profile = Profile::start(
            Profile\Identity::generate(),
            'foo',
            $clock->now()
        ));
        $directory = (require 'src/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->child('section')->child('remote')->definition('http'),
            HttpResource::of(
                $directory->child('section')->child('remote')->definition('http'),
                new Property('request', 'foo'),
                new Property('response', 'bar'),
                new Property('profile', $profile->identity()->toString())
            )
        );

        $this->assertSame(first($adapter->all())->name()->toString(), $identity->toString());
        $profile = $profiles->get($profile->identity());
        $this->assertCount(1, $profile->sections());
        $this->assertSame($identity->toString(), first($profile->sections())->toString());
    }
}
