<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation\Remote;

use Innmind\Profiler\Format;
use Innmind\Filesystem\{
    Adapter,
    Directory,
    File\File,
    File\Content,
};
use Innmind\TimeContinuum\Clock;

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

    public static function of(Adapter $storage, Clock $clock, Directory $profile): self
    {
        return new self($storage, $clock, $profile);
    }

    public function record(Content $process): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        $this->storage->add($this->profile->add(
            Directory\Directory::named('remote-sql')->add(File::named(
                $this->clock->now()->format(new Format),
                $process,
            )),
        ));
    }
}
