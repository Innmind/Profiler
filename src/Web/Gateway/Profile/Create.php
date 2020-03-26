<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Profile;

use Innmind\Profiler\Domain\{
    Entity\Profile,
    Repository\ProfileRepository,
};
use Innmind\Rest\Server\{
    ResourceCreator,
    Definition\HttpResource as ResourceDefinition,
    HttpResource,
    Identity,
};

final class Create implements ResourceCreator
{
    private ProfileRepository $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $profile = Profile::start(
            Profile\Identity::generate(),
            $resource->property('name')->value(),
            $resource->property('started_at')->value()
        );
        $this->repository->add($profile);

        return new Identity\Identity((string) $profile->identity());
    }
}
