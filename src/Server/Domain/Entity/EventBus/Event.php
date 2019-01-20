<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity\EventBus;

use Innmind\TimeContinuum\PointInTimeInterface;

final class Event
{
    private $class;
    private $startedAt;

    public function __construct(string $class, PointInTimeInterface $startedAt)
    {
        $this->class = $class;
        $this->startedAt = $startedAt;
    }

    public function class(): string
    {
        return $this->class;
    }

    public function startedAt(): PointInTimeInterface
    {
        return $this->startedAt;
    }
}
