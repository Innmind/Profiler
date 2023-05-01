<?php
declare(strict_types = 1);

namespace Innmind\Profiler;

use Innmind\TimeContinuum\Format as FormatInterface;

/**
 * @internal
 * @psalm-immutable
 */
final class Format implements FormatInterface
{
    public function toString(): string
    {
        return 'Y-m-dTH:i:s.uP';
    }
}
