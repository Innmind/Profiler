<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Model;

final class Json
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
