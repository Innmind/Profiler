<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway\Exception;

use Innmind\Profiler\Domain\{
    Entity\Exception,
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
    private SectionRepository $exceptions;
    private ProfileRepository $profiles;

    public function __construct(
        SectionRepository $exceptions,
        ProfileRepository $profiles
    ) {
        $this->exceptions = $exceptions;
        $this->profiles = $profiles;
    }

    public function __invoke(
        ResourceDefinition $definition,
        HttpResource $resource
    ): Identity {
        $section = new Exception(
            Section\Identity::generate(Exception::class),
            new Svg($resource->property('graph')->value())
        );
        $this->exceptions->add($section);

        $profile = $this->profiles->get(new Profile\Identity(
            $resource->property('profile')->value()
        ));
        $profile->add($section->identity());
        $this->profiles->add($profile);

        return new Identity\Identity((string) $section->identity());
    }
}
