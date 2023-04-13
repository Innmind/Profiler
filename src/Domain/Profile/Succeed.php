<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Profile;

use Ramsey\Uuid\UuidInterface;

final class Succeed
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UuidInterface $id, string $exit): void
    {
        $_ = $this
            ->repository
            ->get($id)
            ->map(static fn($profile) => $profile->succeed($exit))
            ->match(
                $this->repository->add(...),
                static fn() => null,
            );
    }
}
