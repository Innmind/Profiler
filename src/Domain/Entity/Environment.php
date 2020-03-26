<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\Entity\Section\Identity;
use Innmind\Immutable\Set;
use function Innmind\Immutable\{
    assertSet,
    join,
};

final class Environment implements Section
{
    private Identity $identity;
    private Set $pairs;

    public function __construct(Identity $identity, Set $pairs)
    {
        assertSet('string', $pairs, 2);

        $this->identity = $identity;
        $this->pairs = $pairs;
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    /**
     * @return Set<string>
     */
    public function pairs(): Set
    {
        return $this->pairs;
    }

    public function __toString(): string
    {
        return join("\n", $this->pairs)->toString();
    }
}
