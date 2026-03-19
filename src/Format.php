<?php
declare(strict_types = 1);

namespace Innmind\Profiler;

use Innmind\Time\{
    Format as Base,
    Format\Custom,
};

/**
 * @internal
 * @psalm-immutable
 */
enum Format implements Custom
{
    case internal;

    #[\Override]
    public function normalize(): Base
    {
        return Base::of('Y-m-dTH:i:s.uP');
    }
}
