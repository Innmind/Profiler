<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web;

use Innmind\Profiler\Server\Domain\Entity\{
    AppGraph,
    RequestResponse,
    Exception,
};

final class Templates
{
    public function __invoke(string $section): string
    {
        switch ($section) {
            case AppGraph::class:
                return 'section/app_graph.html.twig';

            case RequestResponse::class:
                return 'section/request_response.html.twig';

            case Exception::class:
                return 'section/exception.html.twig';
        }

        return 'Unknown';
    }
}
