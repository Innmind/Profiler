<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\Entity\Section\Identity;
use Innmind\Immutable\SetInterface;
use function Innmind\Immutable\assertSet;

final class Environment implements Section
{
    private Identity $identity;
    private SetInterface $pairs;

    public function __construct(Identity $identity, SetInterface $pairs)
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
     * @return SetInterface<string>
     */
    public function pairs(): SetInterface
    {
        return $this->pairs;
    }

    public function __toString(): string
    {
        return (string) $this->pairs->join("\n");
    }
}
