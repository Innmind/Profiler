<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Section\Identity,
    Model\Svg,
};

final class Exception implements Section
{
    private $identity;
    private $graph;

    public function __construct(Identity $identity, Svg $graph)
    {
        $this->identity = $identity;
        $this->graph = $graph;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function graph(): Svg
    {
        return $this->graph;
    }
}
