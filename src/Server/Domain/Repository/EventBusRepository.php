<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Repository;

use Innmind\Profiler\Server\Domain\{
    Entity\EventBus,
    Entity\Section\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter,
    File,
    Stream\StringStream,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class EventBusRepository
{
    private $filesystem;

    public function __construct(Adapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(EventBus $section): void
    {
        $this->filesystem->add(new File\File(
            (string) $section->identity(),
            new StringStream(\serialize($section))
        ));
    }

    /**
     * @throws LogicException When section not found
     */
    public function get(Identity $identity): EventBus
    {
        if (!$this->filesystem->has((string) $identity)) {
            throw new LogicException;
        }

        return \unserialize(
            (string) $this->filesystem->get((string) $identity)->content()
        );
    }

    public function remove(Identity $identity): void
    {
        if ($this->filesystem->has((string) $identity)) {
            $this->filesystem->remove((string) $identity);
        }
    }
}
