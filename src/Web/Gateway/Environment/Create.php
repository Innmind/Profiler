<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Environment;

use Innmind\Profiler\Domain\{
    Entity\Environment,
    Entity\Section,
    Entity\Profile,
    Model\Svg,
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
    private SectionRepository $graphs;
    private ProfileRepository $profiles;

    public function __construct(
        SectionRepository $graphs,
        ProfileRepository $profiles
    ) {
        $this->graphs = $graphs;
        $this->profiles = $profiles;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new Environment(
            Section\Identity::generate(Environment::class),
            $resource->property('pairs')->value()
        );
        $this->graphs->add($section);

        $profile = $this->profiles->get(new Profile\Identity(
            $resource->property('profile')->value()
        ));
        $profile->add($section->identity());
        $this->profiles->add($profile);

        return new Identity\Identity($section->identity()->toString());
    }
}
