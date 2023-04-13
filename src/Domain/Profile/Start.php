<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Profile;

use Innmind\Profiler\Domain\Profile;
use Innmind\TimeContinuum\Clock;

final class Start
{
    private Clock $clock;
    private Repository $repository;

    public function __construct(Clock $clock, Repository $repository)
    {
        $this->clock = $clock;
        $this->repository = $repository;
    }

    public function __invoke(string $name): Profile
    {
        $profile = Profile::start($this->clock, $name);
        $this->repository->add($profile);

        return $profile;
    }
}
