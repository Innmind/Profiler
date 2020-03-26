<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Repository;

use Innmind\Profiler\Domain\{
    Repository\SectionRepository,
    Entity\Environment,
    Entity\Section\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter\InMemory,
    File\File,
    Name,
};
use Innmind\Stream\Readable\Stream;
use Innmind\Immutable\Set;
use PHPUnit\Framework\TestCase;

class SectionRepositoryTest extends TestCase
{
    public function testAdd()
    {
        $repository = new SectionRepository(
            $adapter = new InMemory
        );

        $section = new Environment(
            Identity::generate('section'),
            Set::of('string')
        );

        $this->assertNull($repository->add($section));
        $this->assertTrue($adapter->contains(new Name($section->identity()->toString())));
        $this->assertSame(
            \serialize($section),
            $adapter->get(new Name($section->identity()->toString()))->content()->toString(),
        );
    }

    public function testThrowWhenGettingUnknownProfile()
    {
        $repository = new SectionRepository(
            new InMemory
        );

        $this->expectException(LogicException::class);

        $repository->get(Identity::generate('section'));
    }

    public function testGet()
    {
        $repository = new SectionRepository(
            $adapter = new InMemory
        );

        $section = new Environment(
            Identity::generate('section'),
            Set::of('string')
        );
        $adapter->add(File::named(
            $section->identity()->toString(),
            Stream::ofContent(\serialize($section))
        ));

        $this->assertInstanceOf(Environment::class, $repository->get($section->identity()));
        $this->assertNotSame($section, $repository->get($section->identity()));
        $this->assertEquals($section, $repository->get($section->identity()));
    }

    public function testDoNothingWhenRemovingUnknownProfile()
    {
        $repository = new SectionRepository(
            new InMemory
        );

        $this->assertNull($repository->remove(Identity::generate('section')));
    }

    public function testRemove()
    {
        $repository = new SectionRepository(
            $adapter = new InMemory
        );
        $section = new Environment(
            Identity::generate('section'),
            Set::of('string')
        );
        $repository->add($section);

        $this->assertNull($repository->remove($section->identity()));
        $this->assertFalse($adapter->contains(new Name($section->identity()->toString())));
    }
}
