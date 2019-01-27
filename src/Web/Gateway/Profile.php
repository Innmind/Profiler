<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Gateway;

use Innmind\Rest\Server\{
    Gateway,
    ResourceListAccessor,
    ResourceAccessor,
    ResourceCreator,
    ResourceUpdater,
    ResourceRemover,
    ResourceLinker,
    ResourceUnlinker,
    Exception\ActionNotImplemented,
};

final class Profile implements Gateway
{
    private $creator;
    private $updater;

    public function __construct(
        ResourceCreator $creator,
        ResourceUpdater $updater
    ) {
        $this->creator = $creator;
        $this->updater = $updater;
    }

    public function resourceListAccessor(): ResourceListAccessor
    {
        throw new ActionNotImplemented;
    }

    public function resourceAccessor(): ResourceAccessor
    {
        throw new ActionNotImplemented;
    }

    public function resourceCreator(): ResourceCreator
    {
        return $this->creator;
    }

    public function resourceUpdater(): ResourceUpdater
    {
        return $this->updater;
    }

    public function resourceRemover(): ResourceRemover
    {
        throw new ActionNotImplemented;
    }

    public function resourceLinker(): ResourceLinker
    {
        throw new ActionNotImplemented;
    }

    public function resourceUnlinker(): ResourceUnlinker
    {
        throw new ActionNotImplemented;
    }
}
