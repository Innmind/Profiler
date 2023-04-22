<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile;

use Innmind\Immutable\Maybe;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-immutable
 */
final class Id
{
    /** @var non-empty-string */
    private string $value;

    /**
     * @param non-empty-string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function new(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    public static function maybe(string $value): Maybe
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return Maybe::just($value)
            ->filter(Uuid::isValid(...))
            ->map(static fn($value) => new self($value));
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
