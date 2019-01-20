<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity\EventBus;

use Innmind\Profiler\Server\Domain\Entity\EventBus\Event;
use Innmind\TimeContinuum\PointInTimeInterface;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testInterface()
    {
        $event = new Event(
            'class',
            $startedAt = $this->createMock(PointInTimeInterface::class)
        );

        $this->assertSame('class', $event->class());
        $this->assertSame($startedAt, $event->startedAt());
    }
}
