<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Repository;

use Innmind\Profiler\Domain\{
    Entity\Section,
    Entity\Section\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter,
    File,
    Name,
};
use Innmind\Stream\Readable\Stream;

final class SectionRepository
{
    private Adapter $filesystem;

    public function __construct(Adapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(Section $section): void
    {
        $this->filesystem->add(File\File::named(
            $section->identity()->toString(),
            Stream::ofContent(\serialize($section)),
        ));
    }

    /**
     * @throws LogicException When section not found
     */
    public function get(Identity $identity): Section
    {
        if (!$this->filesystem->contains(new Name($identity->toString()))) {
            throw new LogicException;
        }

        return \unserialize(
            $this->filesystem->get(new Name($identity->toString()))->content()->toString(),
        );
    }

    public function remove(Identity $identity): void
    {
        if ($this->filesystem->contains(new Name($identity->toString()))) {
            $this->filesystem->remove(new Name($identity->toString()));
        }
    }
}
