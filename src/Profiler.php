<?php
declare(strict_types = 1);

namespace Innmind\Profiler;

use Innmind\Profiler\{
    Profiler\Mutation,
    Profiler\Load,
    Profile\Id,
};
use Innmind\Filesystem\{
    Adapter,
    Name,
    Directory,
    File\File,
    File\Content,
};
use Innmind\TimeContinuum\{
    Clock,
    Earth\Format\ISO8601,
};
use Innmind\Json\Json;
use Innmind\Immutable\{
    Sequence,
    Maybe,
    Predicate\Instance,
};

final class Profiler
{
    private Adapter $storage;
    private Clock $clock;

    private function __construct(Adapter $storage, Clock $clock)
    {
        $this->storage = $storage;
        $this->clock = $clock;
    }

    public static function of(Adapter $storage, Clock $clock): self
    {
        return new self($storage, $clock);
    }

    public function start(string $name): Id
    {
        $id = Id::new();
        $this->storage->add(
            Directory\Directory::named($id->toString())->add(File::named(
                'start.json',
                Content\Lines::ofContent(Json::encode([
                    'name' => $name,
                    'startedAt' => $this->clock->now()->format(new ISO8601),
                ])),
            )),
        );

        return $id;
    }

    /**
     * @param callable(Mutation): void $mutation
     */
    public function mutate(Id $id, callable $mutation): void
    {
        $_ = $this
            ->storage
            ->get(Name::of($id->toString()))
            ->keep(Instance::of(Directory::class))
            ->match(
                fn($profile) => $mutation(Mutation::of(
                    $this->storage,
                    $this->clock,
                    $profile,
                )),
                static fn() => null,
            );
    }

    /**
     * @return Maybe<Profile>
     */
    public function get(Id $profile): Maybe
    {
        $load = Load::of($this->clock);

        return $this
            ->storage
            ->get(Name::of($profile->toString()))
            ->keep(Instance::of(Directory::class))
            ->flatMap($load);
    }

    /**
     * @return Sequence<Profile>
     */
    public function all(): Sequence
    {
        $load = Load::of($this->clock);

        return $this
            ->storage
            ->root()
            ->files()
            ->keep(Instance::of(Directory::class))
            ->flatMap(static fn($profile) => $load($profile)->match(
                static fn($profile) => Sequence::of($profile),
                static fn() => Sequence::of(),
            ))
            ->sort(
                static fn($a, $b) => $b->startedAt()->format(new ISO8601) <=> $a->startedAt()->format(new ISO8601),
            );
    }
}
