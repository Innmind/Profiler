<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\AppGraph;

use Innmind\Profiler\Domain\{
    Entity\AppGraph,
    Entity\Section,
    Entity\Profile,
    Model\Svg,
    Repository\AppGraphRepository,
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
        AppGraphRepository $graphs,
        ProfileRepository $profiles
    ) {
        $this->graphs = $graphs;
        $this->profiles = $profiles;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new AppGraph(
            Section\Identity::generate(AppGraph::class),
            new Svg($resource->property('graph')->value())
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
