<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain;

use Innmind\OperatingSystem\Filesystem;
use Innmind\Url\{
    PathInterface,
    Path,
};
use Innmind\Immutable\{
    MapInterface,
    Map,
};

function bootstrap(Filesystem $filesystem, PathInterface $storage): MapInterface
{
    return Map::of('string', 'object')
        (
            Entity\Profile::class,
            new Repository\ProfileRepository(
                $filesystem->mount(new Path($storage.'/profiles'))
            )
        )
        (
            Entity\AppGraph::class,
            new Repository\AppGraphRepository(
                $filesystem->mount(new Path($storage.'/sections/app-graph'))
            )
        )
        (
            Entity\Exception::class,
            new Repository\ExceptionRepository(
                $filesystem->mount(new Path($storage.'/sections/exception'))
            )
        )
        (
            Entity\RequestResponse::class,
            new Repository\RequestResponseRepository(
                $filesystem->mount(new Path($storage.'/sections/request-response'))
            )
        );
}
