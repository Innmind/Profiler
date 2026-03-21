<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation;

use Innmind\Profiler\Profiler\Mutation\Remote\{
    Http,
    Sql,
    Processes,
};
use Innmind\Filesystem\{
    Adapter,
    Directory,
};
use Innmind\Time\Clock;

final class Remote
{
    private function __construct(
        private Adapter $storage,
        private Clock $clock,
        private Directory $profile,
    ) {
    }

    /**
     * @internal
     */
    public static function of(Adapter $storage, Clock $clock, Directory $profile): self
    {
        return new self($storage, $clock, $profile);
    }

    public function http(): Http
    {
        return Http::of($this->storage, $this->clock, $this->profile);
    }

    public function sql(): Sql
    {
        return Sql::of($this->storage, $this->clock, $this->profile);
    }

    public function processes(): Processes
    {
        return Processes::of($this->storage, $this->clock, $this->profile);
    }
}
