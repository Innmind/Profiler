<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain\Entity\CommandBus;

use Innmind\Profiler\Server\Domain\Entity\CommandBus\Command;
use Innmind\TimeContinuum\PointInTimeInterface;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testInterface()
    {
        $command = new Command(
            'class',
            $startedAt = $this->createMock(PointInTimeInterface::class)
        );

        $this->assertSame('class', $command->class());
        $this->assertSame($startedAt, $command->startedAt());
    }
}
