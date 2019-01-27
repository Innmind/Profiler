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
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/app-graph'))
            )
        )
        (
            Entity\Exception::class,
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/exception'))
            )
        )
        (
            Entity\Http::class,
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/http'))
            )
        )
        (
            Entity\Environment::class,
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/environment'))
            )
        )
        (
            Entity\Processes::class,
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/processes'))
            )
        )
        (
            Entity\Remote\Http::class,
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/remote-http'))
            )
        )
        (
            // this is not a real class but it's used to separate the processes
            // from the ones executed on remote servers
            Entity\Remote\Processes::class,
            new Repository\SectionRepository(
                $filesystem->mount(new Path($storage.'/sections/remote-processes'))
            )
        );
}
