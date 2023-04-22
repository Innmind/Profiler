<?php
declare(strict_types = 1);

namespace Innmind\Profiler;

use Innmind\Profiler\Profile\{
    Id,
    Status,
    Section,
};
use Innmind\TimeContinuum\{
    PointInTime,
    Earth\Format\ISO8601,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Profile
{
    private Id $id;
    private string $name;
    private PointInTime $startedAt;
    /** @var Sequence<Section> */
    private Sequence $sections;
    private Status $status;
    /** @var Maybe<string> */
    private Maybe $exit;

    /**
     * @param Sequence<Section> $sections
     * @param Maybe<string> $exit
     */
    private function __construct(
        Id $id,
        string $name,
        PointInTime $startedAt,
        Sequence $sections,
        Status $status,
        Maybe $exit,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->startedAt = $startedAt;
        $this->sections = $sections;
        $this->status = $status;
        $this->exit = $exit;
    }

    /**
     * @psalm-pure
     */
    public static function of(
        Id $id,
        string $name,
        PointInTime $startedAt,
    ): self {
        /** @var Maybe<string> */
        $exit = Maybe::nothing();

        return new self(
            $id,
            $name,
            $startedAt,
            Sequence::of(),
            Status::started,
            $exit,
        );
    }

    public function id(): Id
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

    /**
     * @param Sequence<Section> $sections
     */
    public function withSections(Sequence $sections): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->startedAt,
            $sections,
            $this->status,
            $this->exit,
        );
    }

    /**
     * @return Sequence<Section>
     */
    public function sections(): Sequence
    {
        return $this->sections;
    }

    public function withExit(Status $status, string $message): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->startedAt,
            $this->sections,
            $status,
            Maybe::just($message),
        );
    }

    public function status(): Status
    {
        return $this->status;
    }

    /**
     * @return Maybe<string>
     */
    public function exit(): Maybe
    {
        return $this->exit;
    }

    public function toString(): string
    {
        return \sprintf(
            '[%s]%s %s',
            $this->startedAt->format(new ISO8601),
            $this->exit->match(
                static fn($exit) => " [$exit]",
                static fn() => '',
            ),
            $this->name,
        );
    }
}
