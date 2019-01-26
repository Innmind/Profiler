<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway\AppGraph;

use Innmind\Profiler\Server\Domain\{
    Entity\Profile,
    Entity\Section,
    Entity\AppGraph,
    Repository\ProfileRepository,
};
use Innmind\Rest\Server\{
    ResourceLinker,
    Reference,
    Link as ResourceLink,
};

final class Link implements ResourceLinker
{
    private $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Reference $from, ResourceLink ...$links): void
    {
        $section = new Section\Identity((string) $from->identity(), AppGraph::class);

        foreach ($links as $link) {
            if ($link->relationship() !== 'section-of') {
                continue;
            }

            $profile = $this->repository->get(new Profile\Identity(
                (string) $link->reference()->identity()
            ));
            $profile->add($section);
            $this->repository->add($profile);
        }
    }
}
