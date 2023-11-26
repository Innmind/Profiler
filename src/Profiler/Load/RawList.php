<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Load;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\{
    Name,
    Directory,
    File,
};
use Innmind\Immutable\{
    Maybe,
    Predicate\Instance,
};

final class RawList
{
    /** @var non-empty-string */
    private string $name;
    /** @var non-empty-string */
    private string $slug;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $slug
     */
    public function __construct(string $name, string $slug)
    {
        $this->name = $name;
        $this->slug = $slug;
    }

    /**
     * @return Maybe<Section>
     */
    public function __invoke(Directory $profile): Maybe
    {
        return $profile
            ->get(Name::of($this->slug))
            ->keep(Instance::of(Directory::class))
            ->map(
                static fn($directory) => $directory
                    ->all()
                    ->keep(Instance::of(File::class))
                    ->sort(
                        static fn($a, $b) => $b->name()->toString() <=> $a->name()->toString(),
                    ),
            )
            ->map(static fn($files) => $files->map(
                static fn($file) => $file->content(),
            ))
            ->map(fn($contents) => Section\RawList::of(
                $this->name,
                $this->slug,
                $contents,
            ));
    }
}
