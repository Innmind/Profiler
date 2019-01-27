<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity\Remote;

use Innmind\Profiler\Domain\Entity\{
    Section,
    Section\Identity,
    Remote\Http\Call,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class Http implements Section
{
    private $identity;
    private $calls;

    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
        $this->calls = Stream::of(Call::class);
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function add(Call $call): void
    {
        $this->calls = $this->calls->add($call);
    }

    /**
     * @return StreamInterface<Call>
     */
    public function calls(): StreamInterface
    {
        return $this->calls;
    }
}
