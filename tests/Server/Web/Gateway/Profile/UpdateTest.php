<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway\Profile;

use Innmind\Profiler\Server\{
    Web\Gateway\Profile\Update,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile,
};
use Innmind\Rest\Server\{
    ResourceUpdater,
    HttpResource\HttpResource,
    HttpResource\Property,
    Identity\Identity,
};
use Innmind\Filesystem\Adapter\MemoryAdapter;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceUpdater::class,
            new Update(
                new ProfileRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testSuccess()
    {
        $clock = new Earth;
        $update = new Update(
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

        $update(
            $directory->definition('profile'),
            new Identity((string) $profile->identity()),
            HttpResource::of(
                $directory->definition('profile'),
                new Property('success', true)
            )
        );

        $profile = $repository->get($profile->identity());
        $this->assertEquals(Profile\Status::succeeded(), $profile->status());
    }

    public function testFailure()
    {
        $clock = new Earth;
        $update = new Update(
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

        $update(
            $directory->definition('profile'),
            new Identity((string) $profile->identity()),
            HttpResource::of(
                $directory->definition('profile'),
                new Property('success', false)
            )
        );

        $profile = $repository->get($profile->identity());
        $this->assertEquals(Profile\Status::failed(), $profile->status());
    }
}
