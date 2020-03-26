<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain;

use Innmind\OperatingSystem\Filesystem;
use Innmind\Url\Path;
use Innmind\Immutable\Map;

function bootstrap(Filesystem $filesystem, Path $storage): Map
{
    return Map::of('string', 'object')
        (
            Entity\Profile::class,
            new Repository\ProfileRepository(
                $filesystem->mount($storage->resolve(Path::of('profiles/'))),
            ),
        )
        (
            Entity\AppGraph::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/app-graph/'))),
            ),
        )
        (
            Entity\CallGraph::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/call-graph/'))),
            ),
        )
        (
            Entity\Exception::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/exception/'))),
            ),
        )
        (
            Entity\Http::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/http/'))),
            ),
        )
        (
            Entity\Environment::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/environment/'))),
            ),
        )
        (
            Entity\Processes::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/processes/'))),
            ),
        )
        (
            Entity\Remote\Http::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/remote-http/'))),
            ),
        )
        (
            // this is not a real class but it's used to separate the processes
            // from the ones executed on remote servers
            Entity\Remote\Processes::class,
            new Repository\SectionRepository(
                $filesystem->mount($storage->resolve(Path::of('sections/remote-processes/'))),
            ),
        );
}
