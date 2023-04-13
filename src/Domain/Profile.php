<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain;

use Innmind\Profiler\Domain\Profile\{
    Status,
    Section,
};
use Innmind\TimeContinuum\{
    Clock,
    PointInTime,
    Earth\Format\ISO8601,
};
use Innmind\Immutable\Sequence;
use Ramsey\Uuid\{
    Uuid,
    UuidInterface,
};

/**
 * @psalm-immutable
 */
final class Profile
{
    private UuidInterface $id;
    private string $name;
    private PointInTime $startedAt;
    /** @var Sequence<Section> */
    private Sequence $sections;
    private Status $status;
    private ?string $exit;

    /**
     * @param Sequence<Section> $sections
     */
    private function __construct(
        UuidInterface $id,
        string $name,
        PointInTime $startedAt,
        Sequence $sections,
        Status $status,
        ?string $exit,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->startedAt = $startedAt;
        $this->sections = $sections;
        $this->status = $status;
        $this->exit = $exit;
    }

    public static function start(Clock $clock, string $name): self
    {
        return new self(
            Uuid::uuid4(),
            $name,
            $clock->now(),
            Sequence::of(),
            Status::started,
            null,
        );
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function startedAt(): PointInTime
    {
        return $this->startedAt;
    }

    public function closed(): bool
    {
        return $this->status !== Status::started;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function fail(string $message): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->startedAt,
            $this->sections,
            Status::failed,
            $message,
        );
    }

    public function succeed(string $message): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->startedAt,
            $this->sections,
            Status::succeeded,
            $message,
        );
    }

    /**
     * @return Sequence<Section>
     */
    public function sections(): Sequence
    {
        return $this->sections;
    }

    public function toString(): string
    {
        return \sprintf(
            '[%s]%s %s',
            $this->startedAt->format(new ISO8601),
            $this->closed() ? " [{$this->exit}]" : '',
            $this->name,
        );
    }
}
