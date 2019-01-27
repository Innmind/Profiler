<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web\Gateway;

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

final class AppGraph implements Gateway
{
    private $creator;

    public function __construct(ResourceCreator $creator)
    {
        $this->creator = $creator;
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
        throw new ActionNotImplemented;
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
