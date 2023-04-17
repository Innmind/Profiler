<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation;

use Innmind\Filesystem\{
    Adapter,
    Directory,
};
use Innmind\TimeContinuum\Clock;

final class Sections
{
    private Adapter $storage;
    private Clock $clock;
    private Directory $profile;

    private function __construct(Adapter $storage, Clock $clock, Directory $profile)
    {
        $this->storage = $storage;
        $this->clock = $clock;
        $this->profile = $profile;
    }

    public static function of(Adapter $storage, Clock $clock, Directory $profile): self
    {
        return new self($storage, $clock, $profile);
    }

    public function appGraph(): AppGraph
    {
        return AppGraph::of($this->storage, $this->profile);
    }

    public function callGraph(): CallGraph
    {
        return CallGraph::of($this->storage, $this->profile);
    }

    public function environment(): Environment
    {
        return Environment::of($this->storage, $this->profile);
    }

    public function exception(): Exception
    {
        return Exception::of($this->storage, $this->profile);
    }

    public function http(): Http
    {
        return Http::of($this->storage, $this->profile);
    }

    public function processes(): Processes
    {
        return Processes::of($this->storage, $this->clock, $this->profile);
    }

    public function remote(): Remote
    {
        return Remote::of($this->storage, $this->clock, $this->profile);
    }
}
