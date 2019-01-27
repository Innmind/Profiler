<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Immutable\StreamInterface;
use function Innmind\Immutable\assertStream;

final class Processes implements Section
{
    private $identity;
    private $processes;

    public function __construct(
        Section\Identity $identity,
        StreamInterface $processes
    ) {
        assertStream('string', $processes, 2);

        $this->identity = $identity;
        $this->processes = $processes;
    }

    public function identity(): Section\Identity
    {
        return $this->identity;
    }

    /**
     * @return StreamInterface<string>
     */
    public function processes(): StreamInterface
    {
        return $this->processes;
    }
}
