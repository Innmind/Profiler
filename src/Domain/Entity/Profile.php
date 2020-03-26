<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Profile\Identity,
    Entity\Profile\Status,
    Exception\LogicException,
};
use Innmind\TimeContinuum\{
    PointInTime,
    Earth\Format\ISO8601,
};
use Innmind\Immutable\Set;

final class Profile
{
    private Identity $identity;
    private string $name;
    private PointInTime $startedAt;
    private Set $sections;
    private ?Status $status = null;
    private ?string $exit = null;

    private function __construct(
        Identity $identity,
        string $name,
        PointInTime $startedAt
    ) {
        $this->identity = $identity;
        $this->name = $name;
        $this->startedAt = $startedAt;
        $this->sections = Set::of(Section\Identity::class);
    }

    public static function start(
        Identity $identity,
        string $name,
        PointInTime $startedAt
    ): self {
        return new self($identity, $name, $startedAt);
    }

    public function identity(): Identity
    {
        return $this->identity;
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
        return $this->status instanceof Status;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function fail(string $message): void
    {
        $this->status = Status::failed();
        $this->exit = $message;
    }

    public function succeed(string $message): void
    {
        $this->status = Status::succeeded();
        $this->exit = $message;
    }

    public function add(Section\Identity $section): void
    {
        if ($this->closed()) {
            throw new LogicException;
        }

        $this->sections = ($this->sections)($section);
    }

    /**
     * @return Set<Section\Identity>
     */
    public function sections(): Set
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
