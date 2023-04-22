<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler;

use Innmind\Profiler\Profiler\Mutation\Sections;
use Innmind\Filesystem\{
    Adapter,
    Name,
    Directory,
    File\File,
    File\Content,
};
use Innmind\TimeContinuum\Clock;
use Innmind\Json\Json;

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

    public function succeed(string $message): void
    {
        $this->finish($message, true);
    }

    public function fail(string $message): void
    {
        $this->finish($message, false);
    }

    public function sections(): Sections
    {
        return Sections::of($this->storage, $this->clock, $this->profile);
    }

    private function finish(string $message, bool $succeeded): void
    {
        if ($this->profile->contains(Name::of('exit.json'))) {
            return;
        }

        $this->storage->add($this->profile->add(File::named(
            'exit.json',
            Content\Lines::ofContent(Json::encode([
                'message' => $message,
                'succeeded' => $succeeded,
            ])),
        )));
    }
}
