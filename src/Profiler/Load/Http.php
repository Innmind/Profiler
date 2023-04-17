<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Load;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\{
    Name,
    Directory,
};
use Innmind\Immutable\{
    Maybe,
    Predicate\Instance,
};

final class Http
{
    /**
     * @return Maybe<Section>
     */
    public function __invoke(Directory $profile): Maybe
    {
        return $profile
            ->get(Name::of('http'))
            ->keep(Instance::of(Directory::class))
            ->flatMap(
                static fn($http) => $http
                    ->get(Name::of('request.txt'))
                    ->map(static fn($file) => $file->content())
                    ->map(static fn($request) => Section\Http::of(
                        $request,
                        $http
                            ->get(Name::of('response.txt'))
                            ->map(static fn($file) => $file->content()),
                    )),
            );
    }
}
