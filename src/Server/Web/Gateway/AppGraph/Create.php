<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway\AppGraph;

use Innmind\Profiler\Server\Domain\{
    Entity\AppGraph,
    Entity\Section,
    Model\Svg,
    Repository\AppGraphRepository,
};
use Innmind\Rest\Server\{
    ResourceCreator,
    Definition\HttpResource as ResourceDefinition,
    HttpResource,
    Identity,
};

final class Create implements ResourceCreator
{
    private $repository;

    public function __construct(AppGraphRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new AppGraph(
            Section\Identity::generate(AppGraph::class),
            new Svg($resource->property('graph')->value())
        );
        $this->repository->add($section);

        return new Identity\Identity((string) $section->identity());
    }
}
