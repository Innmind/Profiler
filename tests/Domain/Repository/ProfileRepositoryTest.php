<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Repository;

use Innmind\Profiler\Domain\{
    Repository\ProfileRepository,
    Entity\Profile,
    Entity\Profile\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter\InMemory,
    File\File,
    Name,
};
use Innmind\Stream\Readable\Stream;
use Innmind\TimeContinuum\Earth\PointInTime\PointInTime;
use Innmind\Immutable\Set;
use function Innmind\Immutable\first;
use PHPUnit\Framework\TestCase;

class ProfileRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new ProfileRepository(
            $adapter = new InMemory
        );

        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );

        $this->assertNull($repository->add($profile));
        $this->assertTrue($adapter->contains(new Name($profile->identity()->toString())));
        $this->assertSame(
            \serialize($profile),
            $adapter->get(new Name($profile->identity()->toString()))->content()->toString(),
        );
    }

    public function testThrowWhenGettingUnknownProfile()
    {
        $repository = new ProfileRepository(
            new InMemory
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate());
    }

    public function testGet()
    {
        $repository = new ProfileRepository(
            $adapter = new InMemory
        );

        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );
        $adapter->add(File::named(
            $profile->identity()->toString(),
            Stream::ofContent(\serialize($profile))
        ));

        $this->assertInstanceOf(Profile::class, $repository->get($profile->identity()));
        $this->assertNotSame($profile, $repository->get($profile->identity()));
        $this->assertEquals($profile, $repository->get($profile->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new ProfileRepository(
            new InMemory
        );

        $this->assertNull($repository->remove(Identity::generate()));
    }

    public function testRemove()
    {
        $repository = new ProfileRepository(
            $adapter = new InMemory
        );
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );
        $repository->add($profile);

        $this->assertNull($repository->remove($profile->identity()));
        $this->assertFalse($adapter->contains(new Name($profile->identity()->toString())));
    }

    public function testAll()
    {
        $repository = new ProfileRepository(
            new InMemory
        );
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );
        $repository->add($profile);

        $all = $repository->all();

        $this->assertInstanceOf(Set::class, $all);
        $this->assertSame(Profile::class, (string) $all->type());
        $this->assertCount(1, $all);
        $this->assertNotSame($profile, first($all));
        $this->assertEquals($profile, first($all));
    }
}
