<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Repository;

use Innmind\Profiler\Domain\{
    Entity\Profile,
    Entity\Profile\Identity,
    Exception\LogicException,
};
use Innmind\Filesystem\{
    Adapter,
    File,
    Name,
};
use Innmind\Stream\Readable\Stream;
use Innmind\Immutable\Set;

final class ProfileRepository
{
    private Adapter $filesystem;

    public function __construct(Adapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(Profile $profile): void
    {
        $this->filesystem->add(File\File::named(
            $profile->identity()->toString(),
            Stream::ofContent(\serialize($profile)),
        ));
    }

    /**
     * @throws LogicException When profile not found
     */
    public function get(Identity $identity): Profile
    {
        if (!$this->filesystem->contains(new Name($identity->toString()))) {
            throw new LogicException;
        }

        return \unserialize(
            $this->filesystem->get(new Name($identity->toString()))->content()->toString(),
        );
    }

    public function remove(Identity $identity): void
    {
        if ($this->filesystem->contains(new Name($identity->toString()))) {
            $this->filesystem->remove(new Name($identity->toString()));
        }
    }

    /**
     * @return Set<Profile>
     */
    public function all(): Set
    {
        return $this
            ->filesystem
            ->all()
            ->reduce(
                Set::of(Profile::class),
                static function(Set $profiles, File $file): Set {
                    return $profiles->add(
                        \unserialize(
                            $file->content()->toString(),
                        )
                    );
                }
            );
    }
}
