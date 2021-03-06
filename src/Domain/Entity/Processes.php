<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Immutable\Set;
use function Innmind\Immutable\{
    assertSet,
    unwrap,
};

final class Processes implements Section
{
    private Section\Identity $identity;
    private Set $processes;

    public function __construct(Section\Identity $identity, Set $processes)
    {
        assertSet('string', $processes, 2);

        $this->identity = $identity;
        $this->processes = $processes;
    }

    public function identity(): Section\Identity
    {
        return $this->identity;
    }

    /**
     * @return list<string>
     */
    public function processes(): array
    {
        return unwrap($this->processes);
    }
}
