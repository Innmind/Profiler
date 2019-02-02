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

final class Templates
{
    public function __invoke(string $section): string
    {
        switch ($section) {
            case AppGraph::class:
                return 'section/app_graph.html.twig';

            case CallGraph::class:
                return 'section/call_graph.html.twig';

            case Http::class:
                return 'section/http.html.twig';

            case Exception::class:
                return 'section/exception.html.twig';

            case Environment::class:
                return 'section/environment.html.twig';

            case Processes::class:
                return 'section/processes.html.twig';

            case Remote\Http::class:
                return 'section/remote/http.html.twig';

            case Remote\Processes::class:
                return 'section/processes.html.twig';
        }

        return 'Unknown';
    }
}
