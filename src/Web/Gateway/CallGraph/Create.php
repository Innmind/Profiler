<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\CallGraph;

use Innmind\Profiler\Domain\{
    Entity\CallGraph,
    Entity\Section,
    Entity\Profile,
    Model\Json,
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
        $section = new CallGraph(
            Section\Identity::generate(CallGraph::class),
            new Json($resource->property('graph')->value()),
        );
        $this->graphs->add($section);

        $profile = $this->profiles->get(new Profile\Identity(
            $resource->property('profile')->value(),
        ));
        $profile->add($section->identity());
        $this->profiles->add($profile);

        return new Identity\Identity($section->identity()->toString());
    }
}
