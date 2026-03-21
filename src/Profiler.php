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
    File,
    File\Content,
};
use Innmind\Time\{
    Clock,
    Format,
};
use Innmind\Json\Json;
use Innmind\Immutable\{
    Attempt,
    Sequence,
    Maybe,
    Predicate\Instance,
};

final class Profiler
{
    private Adapter $storage;
    private Clock $clock;
    private Load $load;

    private function __construct(Adapter $storage, Clock $clock, Load $load)
    {
        $this->storage = $storage;
        $this->clock = $clock;
        $this->load = $load;
    }

    public static function of(Adapter $storage, Clock $clock, Load $load): self
    {
        return new self($storage, $clock, $load);
    }

    /**
     * @return Attempt<Id>
     */
    #[\NoDiscard]
    public function start(string $name): Attempt
    {
        $id = Id::new();

        return $this
            ->storage
            ->add(
                Directory::named($id->toString())->add(File::named(
                    'start.json',
                    Content::ofString(Json::encode([
                        'name' => $name,
                        'startedAt' => $this->clock->now()->format(Format::iso8601()),
                    ])),
                )),
            )
            ->map(static fn() => $id);
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
        return $this
            ->storage
            ->get(Name::of($profile->toString()))
            ->keep(Instance::of(Directory::class))
            ->flatMap($this->load);
    }

    /**
     * @return Sequence<Profile>
     */
    public function all(): Sequence
    {
        return $this
            ->storage
            ->root()
            ->all()
            ->keep(Instance::of(Directory::class))
            ->map($this->load)
            ->flatMap(static fn($profile) => $profile->toSequence())
            ->sort(
                static fn($a, $b) => $b->startedAt()->format(Format::iso8601()) <=> $a->startedAt()->format(Format::iso8601()),
            );
    }
}
