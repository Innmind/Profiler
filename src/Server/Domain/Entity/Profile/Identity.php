<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity\Profile;

use Ramsey\Uuid\Uuid;

final class Identity
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self((string) Uuid::uuid4());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
