<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation;

use Innmind\Filesystem\{
    Adapter,
    Directory,
    File\File,
    File\Content,
};

final class Http
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

    public function received(Content $request): void
    {
        $this->storage->add($this->profile->add(
            Directory\Directory::named('http')->add(File::named(
                'request.txt',
                $request,
            )),
        ));
    }

    public function respondedWith(Content $response): void
    {
        $this->storage->add($this->profile->add(
            Directory\Directory::named('http')->add(File::named(
                'response.txt',
                $response,
            )),
        ));
    }
}
