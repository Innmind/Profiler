<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Processes;

use Innmind\Profiler\Domain\{
    Entity\Processes,
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
    private $graphs;
    private $profiles;
    private $section;

    public function __construct(
        SectionRepository $graphs,
        ProfileRepository $profiles,
        string $section = null
    ) {
        $this->graphs = $graphs;
        $this->profiles = $profiles;
        $this->section = $section ?? Processes::class;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new Processes(
            Section\Identity::generate($this->section),
            $resource->property('processes')->value()
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
