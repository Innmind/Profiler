<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Domain\Entity\Profile;

interface Identity
{
    public function __toString(): string;
}
