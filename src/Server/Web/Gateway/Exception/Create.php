<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway\Exception;

use Innmind\Profiler\Server\Domain\{
    Entity\Exception,
    Entity\Section,
    Model\Svg,
    Repository\ExceptionRepository,
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

    public function __construct(ExceptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new Exception(
            Section\Identity::generate('exception'),
            new Svg($resource->property('graph')->value())
        );
        $this->repository->add($section);

        return new Identity\Identity((string) $section->identity());
    }
}
