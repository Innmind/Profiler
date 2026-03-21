<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile;

/**
 * @psalm-immutable
 */
enum Status
{
    case started;
    case succeeded;
    case failed;
}
