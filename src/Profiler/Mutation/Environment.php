<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation;

use Innmind\Filesystem\{
    Adapter,
    Directory,
    File,
    File\Content,
    File\Content\Line,
};
use Innmind\Immutable\{
    Map,
    Str,
};

final class Environment
{
    private Adapter $storage;
    private Directory $profile;

    private function __construct(Adapter $storage, Directory $profile)
    {
        $this->storage = $storage;
        $this->profile = $profile;
    }

    public static function of(Adapter $storage, Directory $profile): self
    {
        return new self($storage, $profile);
    }

    /**
     * @param Map<string, string> $pairs
     */
    public function record(Map $pairs): void
    {
        $this->storage->add($this->profile->add(File::named(
            'environment.txt',
            Content::ofLines(
                $pairs
                    ->map(static fn($key, $value) => "$key=$value")
                    ->values()
                    ->map(Str::of(...))
                    ->map(Line::of(...)),
            ),
        )));
    }
}
