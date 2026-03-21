<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler;

use Innmind\Profiler\Profiler\Mutation\Sections;
use Innmind\Filesystem\{
    Adapter,
    Name,
    Directory,
    File,
    File\Content,
};
use Innmind\Time\Clock;
use Innmind\Json\Json;
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};

final class Mutation
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

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function succeed(string $message): Attempt
    {
        return $this->finish($message, true);
    }

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function fail(string $message): Attempt
    {
        return $this->finish($message, false);
    }

    public function sections(): Sections
    {
        return Sections::of($this->storage, $this->clock, $this->profile);
    }

    /**
     * @return Attempt<SideEffect>
     */
    private function finish(string $message, bool $succeeded): Attempt
    {
        if ($this->profile->contains(Name::of('exit.json'))) {
            return Attempt::result(SideEffect::identity);
        }

        return $this->storage->add($this->profile->add(File::named(
            'exit.json',
            Content::ofString(Json::encode([
                'message' => $message,
                'succeeded' => $succeeded,
            ])),
        )));
    }
}
