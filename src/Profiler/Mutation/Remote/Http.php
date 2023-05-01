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

final class Http
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

    public function sent(Content $request): void
    {
        $this->record($request);
    }

    public function got(Content $response): void
    {
        $this->record($response);
    }

    /**
     * No distinction between request and response when persisting because the
     * http client allows for concurrency so we only need to persist in order
     * what we got
     */
    private function record(Content $message): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        $this->storage->add($this->profile->add(
            Directory\Directory::named('remote-http')->add(File::named(
                $this->clock->now()->format(new Format),
                $message,
            )),
        ));
    }
}
