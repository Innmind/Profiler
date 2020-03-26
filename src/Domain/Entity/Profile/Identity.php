<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity\Profile;

use Ramsey\Uuid\Uuid;

final class Identity
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self((string) Uuid::uuid4());
    }

    public function toString(): string
    {
        return $this->value;
    }
}
