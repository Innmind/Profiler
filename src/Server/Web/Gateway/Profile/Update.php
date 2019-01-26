<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway\Profile;

use Innmind\Profiler\Server\Domain\{
    Entity\Profile,
    Repository\ProfileRepository,
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

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(
        ResourceDefinition $definition,
        Identity $identity,
        HttpResource $resource
    ): void {
        $identity = new Profile\Identity((string) $identity);
        $profile = $this->repository->get($identity);

        if ($profile->closed()) {
            return;
        }

        if ($resource->property('success')->value()) {
            $profile->succeed();
        } else {
            $profile->fail();
        }

        $this->repository->add($profile);
    }
}
