<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Remote\Http;

use Innmind\Profiler\Domain\{
    Entity\Remote\Http,
    Entity\Remote\Http\Call,
    Entity\Section,
    Entity\Profile,
    Repository\SectionRepository,
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
    private SectionRepository $requests;
    private ProfileRepository $profiles;

    public function __construct(
        SectionRepository $requests,
        ProfileRepository $profiles
    ) {
        $this->requests = $requests;
        $this->profiles = $profiles;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new Http(
            Section\Identity::generate(Http::class)
        );
        $section->add(new Call(
            $resource->property('request')->value(),
            $resource->property('response')->value()
        ));
        $this->requests->add($section);

        $profile = $this->profiles->get(new Profile\Identity(
            $resource->property('profile')->value()
        ));
        $profile->add($section->identity());
        $this->profiles->add($profile);

        return new Identity\Identity($section->identity()->toString());
    }
}
