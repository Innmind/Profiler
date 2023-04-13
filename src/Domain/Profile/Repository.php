<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Profile;

use Innmind\Profiler\Domain\Profile;
use Innmind\Filesystem\{
    Adapter,
    File\Content,
    File\File,
    Name,
};
use Innmind\Immutable\{
    Maybe,
    Sequence,
    Predicate\Instance,
};
use Ramsey\Uuid\{
    UuidInterface,
    Uuid,
};

final class Repository
{
    private Adapter $filesystem;

    public function __construct(Adapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(Profile $profile): void
    {
        $this->filesystem->add(File::named(
            $profile->id()->toString(),
            Content\Lines::ofContent(\serialize($profile)),
        ));
    }

    /**
     * @return Maybe<Profile>
     */
    public function get(UuidInterface $id): Maybe
    {
        return $this
            ->filesystem
            ->get(Name::of($id->toString()))
            ->map(static fn($file) => $file->content()->toString())
            ->map(\unserialize(...))
            ->keep(Instance::of(Profile::class));
    }

    /**
     * @return Sequence<Profile>
     */
    public function all(): Sequence
    {
        return $this
            ->filesystem
            ->root()
            ->files()
            ->filter(static fn($file) => Uuid::isValid($file->name()->toString()))
            ->map(static fn($file) => $file->content()->toString())
            ->map(\unserialize(...))
            ->keep(Instance::of(Profile::class));
    }
}
