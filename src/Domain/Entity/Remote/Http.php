<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity\Remote;

use Innmind\Profiler\Domain\Entity\{
    Section,
    Section\Identity,
    Remote\Http\Call,
};
use Innmind\Immutable\Sequence;
use function Innmind\Immutable\unwrap;

final class Http implements Section
{
    private Identity $identity;
    private Sequence $calls;

    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
        $this->calls = Sequence::of(Call::class);
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function add(Call $call): void
    {
        $this->calls = ($this->calls)($call);
    }

    /**
     * @return list<Call>
     */
    public function calls(): array
    {
        return unwrap($this->calls);
    }
}
