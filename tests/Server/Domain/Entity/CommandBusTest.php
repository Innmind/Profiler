<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity;

use Innmind\Profiler\Server\Domain\Entity\{
    CommandBus,
    CommandBus\Command,
    Section\Identity,
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Immutable\StreamInterface;
use PHPUnit\Framework\TestCase;

class CommandBusTest extends TestCase
{
    public function testInterface()
    {
        $bus = new CommandBus(
            $identity = $this->createMock(Identity::class)
        );

        $this->assertSame($identity, $bus->identity());
        $this->assertInstanceOf(StreamInterface::class, $bus->commands());
        $this->assertSame(Command::class, (string) $bus->commands()->type());
        $this->assertCount(0, $bus->commands());
        $this->assertNull($bus->add($command = new Command(
            'foo',
            $this->createMock(PointInTimeInterface::class)
        )));
        $this->assertSame([$command], $bus->commands()->toPrimitive());
    }
}
