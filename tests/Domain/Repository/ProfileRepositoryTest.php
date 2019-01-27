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
    Adapter\MemoryAdapter,
    File\File,
    Stream\StringStream,
};
use Innmind\TimeContinuum\PointInTime\Earth\PointInTime;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class ProfileRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new ProfileRepository(
            $adapter = new MemoryAdapter
        );

        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );

        $this->assertNull($repository->add($profile));
        $this->assertTrue($adapter->has((string) $profile->identity()));
        $this->assertSame(
            \serialize($profile),
            (string) $adapter->get((string) $profile->identity())->content()
        );
    }

    public function testThrowWhenGettingUnknownProfile()
    {
        $repository = new ProfileRepository(
            new MemoryAdapter
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate());
    }

    public function testGet()
    {
        $repository = new ProfileRepository(
            $adapter = new MemoryAdapter
        );

        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );
        $adapter->add(new File(
            (string) $profile->identity(),
            new StringStream(\serialize($profile))
        ));

        $this->assertInstanceOf(Profile::class, $repository->get($profile->identity()));
        $this->assertNotSame($profile, $repository->get($profile->identity()));
        $this->assertEquals($profile, $repository->get($profile->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new ProfileRepository(
            new MemoryAdapter
        );

        $this->assertNull($repository->remove(Identity::generate()));
    }

    public function testRemove()
    {
        $repository = new ProfileRepository(
            $adapter = new MemoryAdapter
        );
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );
        $repository->add($profile);

        $this->assertNull($repository->remove($profile->identity()));
        $this->assertFalse($adapter->has((string) $profile->identity()));
    }

    public function testAll()
    {
        $repository = new ProfileRepository(
            new MemoryAdapter
        );
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            new PointInTime('2019-01-01T00:00:00+01:00')
        );
        $repository->add($profile);

        $all = $repository->all();

        $this->assertInstanceOf(SetInterface::class, $all);
        $this->assertSame(Profile::class, (string) $all->type());
        $this->assertCount(1, $all);
        $this->assertNotSame($profile, $all->current());
        $this->assertEquals($profile, $all->current());
    }
}
