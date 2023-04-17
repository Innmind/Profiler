<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Load;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\{
    Name,
    Directory,
};
use Innmind\Immutable\Maybe;

final class CallGraph
{
    /**
     * @return Maybe<Section>
     */
    public function __invoke(Directory $profile): Maybe
    {
        return $profile
            ->get(Name::of('call-graph.json'))
            ->map(static fn($file) => $file->content())
            ->map(Section\CallGraph::of(...));
    }
}
