<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web;

use Innmind\Profiler\Server\Domain\Entity\{
    AppGraph,
    RequestResponse,
    Exception,
};

final class Names
{
    public function __invoke(string $section): string
    {
        switch ($section) {
            case AppGraph::class:
                return 'App graph';

            case RequestResponse::class:
                return 'Request / Response';

            case Exception::class:
                return 'Exception';
        }

        return 'Unknown';
    }
}
