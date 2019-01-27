<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Domain\Entity\{
    AppGraph,
    Http,
    Exception,
    Environment,
    Processes,
    Remote,
};

final class Names
{
    public function __invoke(string $section): string
    {
        switch ($section) {
            case AppGraph::class:
                return 'App graph';

            case Http::class:
                return 'Http';

            case Exception::class:
                return 'Exception';

            case Environment::class:
                return 'Environment';

            case Processes::class:
                return 'Processes';

            case Remote\Http::class:
                return 'Remote / Http';
        }

        return 'Unknown';
    }
}
