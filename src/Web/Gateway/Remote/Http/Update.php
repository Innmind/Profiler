<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Remote\Http;

use Innmind\Profiler\Domain\{
    Entity\Remote\Http,
    Entity\Remote\Http\Call,
    Entity\Section,
    Repository\SectionRepository,
};
use Innmind\Rest\Server\{
    ResourceUpdater,
    Definition\HttpResource as ResourceDefinition,
    HttpResource,
    Identity,
};

final class Update implements ResourceUpdater
{
    private SectionRepository $repository;

    public function __construct(SectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        Identity $identity,
        HttpResource $resource
    ): void {
        $identity = new Section\Identity($identity->toString(), Http::class);
        $section = $this->repository->get($identity);

        $section->add(new Call(
            $resource->property('request')->value(),
            $resource->property('response')->value(),
        ));

        $this->repository->add($section);
    }
}
