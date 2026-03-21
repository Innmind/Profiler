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
    public function sent(Content $request): Attempt
    {
        return $this->record($request);
    }

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function got(Content $response): Attempt
    {
        return $this->record($response);
    }

    /**
     * No distinction between request and response when persisting because the
     * http client allows for concurrency so we only need to persist in order
     * what we got
     *
     * @return Attempt<SideEffect>
     */
    private function record(Content $message): Attempt
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return $this->storage->add($this->profile->add(
            Directory::named('remote-http')->add(File::named(
                $this->clock->now()->format(Format::internal),
                $message,
            )),
        ));
    }
}
