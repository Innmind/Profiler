<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity\Section;

interface Identity
{
    public function name(): string;
    public function __toString(): string;
}
