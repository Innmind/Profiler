<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Repository;

use Innmind\Profiler\Domain\{
    Repository\AppGraphRepository,
    Entity\AppGraph,
    Entity\Section\Identity,
    Model\Svg,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter\MemoryAdapter,
    File\File,
    Stream\StringStream,
};
use PHPUnit\Framework\TestCase;

class AppGraphRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new AppGraphRepository(
            $adapter = new MemoryAdapter
        );

        $section = new AppGraph(
            Identity::generate('section'),
            new Svg('foo')
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
        $repository = new AppGraphRepository(
            new MemoryAdapter
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate('section'));
    }

    public function testGet()
    {
        $repository = new AppGraphRepository(
            $adapter = new MemoryAdapter
        );

        $section = new AppGraph(
            Identity::generate('section'),
            new Svg('foo')
        );
        $adapter->add(new File(
            (string) $section->identity(),
            new StringStream(\serialize($section))
        ));

        $this->assertInstanceOf(AppGraph::class, $repository->get($section->identity()));
        $this->assertNotSame($section, $repository->get($section->identity()));
        $this->assertEquals($section, $repository->get($section->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new AppGraphRepository(
            new MemoryAdapter
        );

        $this->assertNull($repository->remove(Identity::generate('section')));
    }

    public function testRemove()
    {
        $repository = new AppGraphRepository(
            $adapter = new MemoryAdapter
        );
        $section = new AppGraph(
            Identity::generate('section'),
            new Svg('foo')
        );
        $repository->add($section);

        $this->assertNull($repository->remove($section->identity()));
        $this->assertFalse($adapter->has((string) $section->identity()));
    }
}