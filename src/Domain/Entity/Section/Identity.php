<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity\Section;

use Ramsey\Uuid\Uuid;

final class Identity
{
    private string $value;
    private string $section;

    public function __construct(string $value, string $section)
    {
        $this->value = $value;
        $this->section = $section;
    }

    public static function generate(string $section): self
    {
        return new self((string) Uuid::uuid4(), $section);
    }

    public function section(): string
    {
        return $this->section;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
