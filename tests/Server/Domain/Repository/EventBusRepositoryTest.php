<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Repository;

use Innmind\Profiler\Server\Domain\{
    Repository\EventBusRepository,
    Entity\EventBus,
    Entity\Section\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter\MemoryAdapter,
    File\File,
    Stream\StringStream,
};
use PHPUnit\Framework\TestCase;

class EventBusRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new EventBusRepository(
            $adapter = new MemoryAdapter
        );

        $section = new EventBus(
            Identity::generate('section')
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
        $repository = new EventBusRepository(
            new MemoryAdapter
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate('section'));
    }

    public function testGet()
    {
        $repository = new EventBusRepository(
            $adapter = new MemoryAdapter
        );

        $section = new EventBus(
            Identity::generate('section')
        );
        $adapter->add(new File(
            (string) $section->identity(),
            new StringStream(\serialize($section))
        ));

        $this->assertInstanceOf(EventBus::class, $repository->get($section->identity()));
        $this->assertNotSame($section, $repository->get($section->identity()));
        $this->assertEquals($section, $repository->get($section->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new EventBusRepository(
            new MemoryAdapter
        );

        $this->assertNull($repository->remove(Identity::generate('section')));
    }

    public function testRemove()
    {
        $repository = new EventBusRepository(
            $adapter = new MemoryAdapter
        );
        $section = new EventBus(
            Identity::generate('section')
        );
        $repository->add($section);

        $this->assertNull($repository->remove($section->identity()));
        $this->assertFalse($adapter->has((string) $section->identity()));
    }
}
