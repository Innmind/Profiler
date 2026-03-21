<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation\Remote;

use Innmind\Profiler\Format;
use Innmind\Filesystem\{
    Adapter,
    Directory,
    File,
    File\Content,
};
use Innmind\Time\Clock;
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};

final class Sql
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

    /**
     * @internal
     */
    public static function of(Adapter $storage, Clock $clock, Directory $profile): self
    {
        return new self($storage, $clock, $profile);
    }

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function record(Content $process): Attempt
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return $this->storage->add($this->profile->add(
            Directory::named('remote-sql')->add(File::named(
                $this->clock->now()->format(Format::internal),
                $process,
            )),
        ));
    }
}
