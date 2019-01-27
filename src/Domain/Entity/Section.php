<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

interface Section
{
    public function identity(): Section\Identity;
}
