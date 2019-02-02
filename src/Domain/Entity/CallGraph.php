<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Section\Identity,
    Model\Json,
};

final class CallGraph implements Section
{
    private $identity;
    private $graph;

    public function __construct(Identity $identity, Json $graph)
    {
        $this->identity = $identity;
        $this->graph = $graph;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function graph(): Json
    {
        return $this->graph;
    }
}
