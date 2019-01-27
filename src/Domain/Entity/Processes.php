<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Immutable\SetInterface;
use function Innmind\Immutable\assertSet;

final class Processes implements Section
{
    private $identity;
    private $processes;

    public function __construct(
        Section\Identity $identity,
        SetInterface $processes
    ) {
        assertSet('string', $processes, 2);

        $this->identity = $identity;
        $this->processes = $processes;
    }

    public function identity(): Section\Identity
    {
        return $this->identity;
    }

    /**
     * @return SetInterface<string>
     */
    public function processes(): SetInterface
    {
        return $this->processes;
    }
}
