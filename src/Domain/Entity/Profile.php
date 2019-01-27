<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Profile\Identity,
    Entity\Profile\Status,
    Exception\LogicException,
};
use Innmind\TimeContinuum\{
    PointInTimeInterface,
    Format\ISO8601,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class Profile
{
    private $identity;
    private $name;
    private $startedAt;
    private $sections;
    private $status;
    private $exit;

    private function __construct(
        Identity $identity,
        string $name,
        PointInTimeInterface $startedAt
    ) {
        $this->identity = $identity;
        $this->name = $name;
        $this->startedAt = $startedAt;
        $this->sections = Set::of(Section\Identity::class);
    }

    public static function start(
        Identity $identity,
        string $name,
        PointInTimeInterface $startedAt
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

    public function startedAt(): PointInTimeInterface
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

        $this->sections = $this->sections->add($section);
    }

    /**
     * @return SetInterface<Section\Identity>
     */
    public function sections(): SetInterface
    {
        return $this->sections;
    }

    public function __toString(): string
    {
        return \sprintf(
            '[%s]%s %s',
            $this->startedAt->format(new ISO8601),
            $this->closed() ? " [{$this->exit}]" : '',
            $this->name
        );
    }
}