<?php
declare(strict_types = 1);

namespace Innmind\Profiler;

use Innmind\Profiler\Profile\{
    Id,
    Status,
    Section,
};
use Innmind\Time\{
    Point,
    Format,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
};

/**
 * @internal
 * @psalm-immutable
 */
final class Profile
{
    /**
     * @param Sequence<Section> $sections
     * @param Maybe<string> $exit
     */
    private function __construct(
        private Id $id,
        private string $name,
        private Point $startedAt,
        private Sequence $sections,
        private Status $status,
        private Maybe $exit,
    ) {
    }

    /**
     * @internal
     *
     * @psalm-pure
     */
    public static function of(
        Id $id,
        string $name,
        Point $startedAt,
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

    public function startedAt(): Point
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
            $this->startedAt->format(Format::iso8601()),
            $this->exit->match(
                static fn($exit) => " [$exit]",
                static fn() => '',
            ),
            $this->name,
        );
    }
}
