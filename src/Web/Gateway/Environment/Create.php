<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Environment;

use Innmind\Profiler\Domain\{
    Entity\Environment,
    Entity\Section,
    Entity\Profile,
    Model\Svg,
    Repository\EnvironmentRepository,
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
    private $graphs;
    private $profiles;

    public function __construct(
        EnvironmentRepository $graphs,
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

        return new Identity\Identity((string) $section->identity());
    }
}
