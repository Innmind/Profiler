<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity\Profile;

final class Status
{
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function failed(): self
    {
        return new self('failed');
    }

    public static function succeeded(): self
    {
        return new self('succeeded');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
