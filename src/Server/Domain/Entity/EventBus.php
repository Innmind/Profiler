<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\Entity\{
    Section\Identity,
    EventBus\Event,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class EventBus
{
    private $identity;
    private $events;

    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
        $this->events = Stream::of(Event::class);
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function add(Event $event): void
    {
        $this->events = $this->events->add($event);
    }

    /**
     * @return StreamInterface<Event>
     */
    public function events(): StreamInterface
    {
        return $this->events;
    }
}
