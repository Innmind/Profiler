<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway\RequestResponse;

use Innmind\Profiler\Server\Domain\{
    Entity\RequestResponse,
    Entity\Section,
    Repository\RequestResponseRepository,
};
use Innmind\Rest\Server\{
    ResourceUpdater,
    Definition\HttpResource as ResourceDefinition,
    HttpResource,
    Identity,
};

final class Update implements ResourceUpdater
{
    private $repository;

    public function __construct(RequestResponseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        Identity $identity,
        HttpResource $resource
    ): void {
        $identity = new Section\Identity((string) $identity, RequestResponse::class);
        $section = $this->repository->get($identity);

        $section->respondedWith(
            $resource->property('response')->value()
        );

        $this->repository->add($section);
    }
}
