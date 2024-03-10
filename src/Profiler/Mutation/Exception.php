<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation;

use Innmind\Filesystem\{
    Adapter,
    Directory,
    File,
    File\Content,
};

final class Exception
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

    public function record(Content $svg): void
    {
        $this->storage->add($this->profile->add(File::named(
            'exception.svg',
            $svg,
        )));
    }
}
