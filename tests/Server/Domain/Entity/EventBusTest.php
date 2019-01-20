<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\Entity\{
    EventBus,
    EventBus\Event,
    Section\Identity,
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Immutable\StreamInterface;
use PHPUnit\Framework\TestCase;

class EventBusTest extends TestCase
{
    public function testInterface()
    {
        $bus = new EventBus(
            $identity = Identity::generate('event-bus')
        );

        $this->assertSame($identity, $bus->identity());
        $this->assertInstanceOf(StreamInterface::class, $bus->events());
        $this->assertSame(Event::class, (string) $bus->events()->type());
        $this->assertCount(0, $bus->events());
        $this->assertNull($bus->add($event = new Event(
            'foo',
            $this->createMock(PointInTimeInterface::class)
        )));
        $this->assertSame([$event], $bus->events()->toPrimitive());
    }
}
