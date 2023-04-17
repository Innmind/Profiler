<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile;

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
        if (!Uuid::isValid($value)) {
            throw new \DomainException($value);
        }

        $this->value = $value;
    }

    public static function new(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @psalm-pure
     */
    public static function of(string $value): self
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return new self($value);
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
