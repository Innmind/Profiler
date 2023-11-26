<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Load;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\{
    Name,
    Directory,
    File,
};
use Innmind\Immutable\{
    Maybe,
    Predicate\Instance,
};

final class Exception
{
    /**
     * @return Maybe<Section>
     */
    public function __invoke(Directory $profile): Maybe
    {
        return $profile
            ->get(Name::of('exception.svg'))
            ->keep(Instance::of(File::class))
            ->map(static fn($file) => $file->content())
            ->map(Section\Exception::of(...));
    }
}
