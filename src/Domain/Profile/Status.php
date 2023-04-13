<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Profile;

enum Status
{
    case started;
    case succeeded;
    case failed;
}
