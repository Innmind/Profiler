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
    Attempt,
    SideEffect,
};

final class Environment
{
    private function __construct(
        private Adapter $storage,
        private Directory $profile,
    ) {
    }

    /**
     * @internal
     */
    public static function of(Adapter $storage, Directory $profile): self
    {
        return new self($storage, $profile);
    }

    /**
     * @param Map<string, string> $pairs
     *
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function record(Map $pairs): Attempt
    {
        return $this->storage->add($this->profile->add(File::named(
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
