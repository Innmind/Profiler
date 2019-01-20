<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Repository;

use Innmind\Profiler\Server\Domain\{
    Repository\ExceptionRepository,
    Entity\Exception,
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

class ExceptionRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new ExceptionRepository(
            $adapter = new MemoryAdapter
        );

        $section = new Exception(
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
        $repository = new ExceptionRepository(
            new MemoryAdapter
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate('section'));
    }

    public function testGet()
    {
        $repository = new ExceptionRepository(
            $adapter = new MemoryAdapter
        );

        $section = new Exception(
            Identity::generate('section'),
            new Svg('foo')
        );
        $adapter->add(new File(
            (string) $section->identity(),
            new StringStream(\serialize($section))
        ));

        $this->assertInstanceOf(Exception::class, $repository->get($section->identity()));
        $this->assertNotSame($section, $repository->get($section->identity()));
        $this->assertEquals($section, $repository->get($section->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new ExceptionRepository(
            new MemoryAdapter
        );

        $this->assertNull($repository->remove(Identity::generate('section')));
    }

    public function testRemove()
    {
        $repository = new ExceptionRepository(
            $adapter = new MemoryAdapter
        );
        $section = new Exception(
            Identity::generate('section'),
            new Svg('foo')
        );
        $repository->add($section);

        $this->assertNull($repository->remove($section->identity()));
        $this->assertFalse($adapter->has((string) $section->identity()));
    }
}
