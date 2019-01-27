<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway\Environment;

use Innmind\Profiler\{
    Web\Gateway\Environment\Create,
    Domain\Repository\EnvironmentRepository,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile,
};
use Innmind\Rest\Server\{
    ResourceCreator,
    HttpResource\HttpResource,
    HttpResource\Property,
};
use Innmind\Filesystem\Adapter\MemoryAdapter;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceCreator::class,
            new Create(
                new EnvironmentRepository(
                    new MemoryAdapter
                ),
                new ProfileRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new EnvironmentRepository(
                $adapter = new MemoryAdapter
            ),
            $profiles = new ProfileRepository(
                new MemoryAdapter
            )
        );
        $profiles->add($profile = Profile::start(
            Profile\Identity::generate(),
            'foo',
            $clock->now()
        ));
        $directory = (require 'src/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->child('section')->definition('environment'),
            HttpResource::of(
                $directory->child('section')->definition('environment'),
                new Property('pairs', Set::of('string')),
                new Property('profile', (string) $profile->identity())
            )
        );

        $this->assertSame($adapter->all()->key(), (string) $identity);
        $profile = $profiles->get($profile->identity());
        $this->assertCount(1, $profile->sections());
        $this->assertSame((string) $identity, (string) $profile->sections()->current());
    }
}
