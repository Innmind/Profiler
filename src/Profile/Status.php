<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile;

/**
 * @internal
 * @psalm-immutable
 */
enum Status
{
    case started;
    case succeeded;
    case failed;
}
