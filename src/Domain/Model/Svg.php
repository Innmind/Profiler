<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Model;

use Innmind\Immutable\Str;

final class Svg
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function dataUri(): Str
    {
        return Str::of('data:image/svg+xml;base64,'.\base64_encode($this->value));
    }

    public function toString(): string
    {
        return $this->value;
    }
}
