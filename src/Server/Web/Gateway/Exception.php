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

final class Exception implements Gateway
{
    private $creator;
    private $linker;

    public function __construct(
        ResourceCreator $creator,
        ResourceLinker $linker
    ) {
        $this->creator = $creator;
        $this->linker = $linker;
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
        return $this->linker;
    }

    public function resourceUnlinker(): ResourceUnlinker
    {
        throw new ActionNotImplemented;
    }
}
