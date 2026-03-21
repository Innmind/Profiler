<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler\Mutation;

use Innmind\Filesystem\{
    Adapter,
    Directory,
    File,
    File\Content,
};
use Innmind\Immutable\{
    Attempt,
    SideEffect,
};

final class Http
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
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function received(Content $request): Attempt
    {
        return $this->storage->add($this->profile->add(
            Directory::named('http')->add(File::named(
                'request.txt',
                $request,
            )),
        ));
    }

    /**
     * @return Attempt<SideEffect>
     */
    #[\NoDiscard]
    public function respondedWith(Content $response): Attempt
    {
        return $this->storage->add($this->profile->add(
            Directory::named('http')->add(File::named(
                'response.txt',
                $response,
            )),
        ));
    }
}
