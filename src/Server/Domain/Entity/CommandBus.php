<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\Entity\{
    Section\Identity,
    CommandBus\Command,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class CommandBus
{
    private $identity;
    private $commands;

    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
        $this->commands = Stream::of(Command::class);
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function add(Command $command): void
    {
        $this->commands = $this->commands->add($command);
    }

    /**
     * @return StreamInterface<Command>
     */
    public function commands(): StreamInterface
    {
        return $this->commands;
    }
}
