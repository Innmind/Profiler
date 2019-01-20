<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Repository;

use Innmind\Profiler\Server\Domain\{
    Repository\RequestResponseRepository,
    Entity\RequestResponse,
    Entity\Section\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter\MemoryAdapter,
    File\File,
    Stream\StringStream,
};
use PHPUnit\Framework\TestCase;

class RequestResponseRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new RequestResponseRepository(
            $adapter = new MemoryAdapter
        );

        $section = RequestResponse::received(
            Identity::generate('section'),
            'request'
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
        $repository = new RequestResponseRepository(
            new MemoryAdapter
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate('section'));
    }

    public function testGet()
    {
        $repository = new RequestResponseRepository(
            $adapter = new MemoryAdapter
        );

        $section = RequestResponse::received(
            Identity::generate('section'),
            'request'
        );
        $adapter->add(new File(
            (string) $section->identity(),
            new StringStream(\serialize($section))
        ));

        $this->assertInstanceOf(RequestResponse::class, $repository->get($section->identity()));
        $this->assertNotSame($section, $repository->get($section->identity()));
        $this->assertEquals($section, $repository->get($section->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new RequestResponseRepository(
            new MemoryAdapter
        );

        $this->assertNull($repository->remove(Identity::generate('section')));
    }

    public function testRemove()
    {
        $repository = new RequestResponseRepository(
            $adapter = new MemoryAdapter
        );
        $section = RequestResponse::received(
            Identity::generate('section'),
            'request'
        );
        $repository->add($section);

        $this->assertNull($repository->remove($section->identity()));
        $this->assertFalse($adapter->has((string) $section->identity()));
    }
}
