<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\RequestResponse;

use Innmind\Profiler\Domain\{
    Entity\RequestResponse,
    Entity\Section,
    Entity\Profile,
    Repository\RequestResponseRepository,
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
    private $requests;
    private $profiles;

    public function __construct(
        RequestResponseRepository $requests,
        ProfileRepository $profiles
    ) {
        $this->requests = $requests;
        $this->profiles = $profiles;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = RequestResponse::received(
            Section\Identity::generate(RequestResponse::class),
            $resource->property('request')->value()
        );
        $this->requests->add($section);

        $profile = $this->profiles->get(new Profile\Identity(
            $resource->property('profile')->value()
        ));
        $profile->add($section->identity());
        $this->profiles->add($profile);

        return new Identity\Identity((string) $section->identity());
    }
}
