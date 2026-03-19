<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Profiler;
use Innmind\Http\Response;
use Innmind\DI\Service;
use Innmind\Immutable\Attempt;

/**
 * @template T of object
 * @implements Service<T>
 */
enum Services implements Service
{
    case profiler;
    case listProfiles;
    case showProfile;

    /**
     * @return self<Profiler>
     */
    public static function profiler(): self
    {
        /** @var self<Profiler> */
        return self::profiler;
    }

    /**
     * @internal
     *
     * @return self<object&(callable(mixed...): Attempt<Response>)>
     */
    public static function listProfiles(): self
    {
        /** @var self<object&(callable(mixed...): Attempt<Response>)> */
        return self::listProfiles;
    }

    /**
     * @internal
     *
     * @return self<object&(callable(mixed...): Attempt<Response>)>
     */
    public static function showProfile(): self
    {
        /** @var self<object&(callable(mixed...): Attempt<Response>)> */
        return self::showProfile;
    }
}
