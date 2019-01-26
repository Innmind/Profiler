<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway\RequestResponse;

use Innmind\Profiler\Server\Domain\{
    Entity\RequestResponse,
    Entity\Section,
    Repository\RequestResponseRepository,
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

    public function __construct(RequestResponseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = RequestResponse::received(
            Section\Identity::generate(RequestResponse::class),
            $resource->property('request')->value()
        );
        $this->repository->add($section);

        return new Identity\Identity((string) $section->identity());
    }
}
