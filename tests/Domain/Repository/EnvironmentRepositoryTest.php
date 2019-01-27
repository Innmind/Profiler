<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Repository;

use Innmind\Profiler\Domain\{
    Repository\EnvironmentRepository,
    Entity\Environment,
    Entity\Section\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter\MemoryAdapter,
    File\File,
    Stream\StringStream,
};
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class EnvironmentRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new EnvironmentRepository(
            $adapter = new MemoryAdapter
        );

        $section = new Environment(
            Identity::generate('section'),
            Set::of('string')
        );

        $this->assertNull($repository->add($section));
        $this->assertTrue($adapter->has((string) $section->identity()));
        $this->assertSame(
            \serialize($section),
            (string) $adapter->get((string) $section->identity())->content()
        );
    }

    public function testThrowWhenGettingUnknownProfile()
    {
        $repository = new EnvironmentRepository(
            new MemoryAdapter
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate('section'));
    }

    public function testGet()
    {
        $repository = new EnvironmentRepository(
            $adapter = new MemoryAdapter
        );

        $section = new Environment(
            Identity::generate('section'),
            Set::of('string')
        );
        $adapter->add(new File(
            (string) $section->identity(),
            new StringStream(\serialize($section))
        ));

        $this->assertInstanceOf(Environment::class, $repository->get($section->identity()));
        $this->assertNotSame($section, $repository->get($section->identity()));
        $this->assertEquals($section, $repository->get($section->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new EnvironmentRepository(
            new MemoryAdapter
        );

        $this->assertNull($repository->remove(Identity::generate('section')));
    }

    public function testRemove()
    {
        $repository = new EnvironmentRepository(
            $adapter = new MemoryAdapter
        );
        $section = new Environment(
            Identity::generate('section'),
            Set::of('string')
        );
        $repository->add($section);

        $this->assertNull($repository->remove($section->identity()));
        $this->assertFalse($adapter->has((string) $section->identity()));
    }
}
