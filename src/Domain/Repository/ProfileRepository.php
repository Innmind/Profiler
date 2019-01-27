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
    Stream\StringStream,
};
use Innmind\Immutable\{
    SetInterface,
    Set,
};

final class ProfileRepository
{
    private $filesystem;

    public function __construct(Adapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function add(Profile $profile): void
    {
        $this->filesystem->add(new File\File(
            (string) $profile->identity(),
            new StringStream(\serialize($profile))
        ));
    }

    /**
     * @throws LogicException When profile not found
     */
    public function get(Identity $identity): Profile
    {
        if (!$this->filesystem->has((string) $identity)) {
            throw new LogicException;
        }

        return \unserialize(
            (string) $this->filesystem->get((string) $identity)->content()
        );
    }

    public function remove(Identity $identity): void
    {
        if ($this->filesystem->has((string) $identity)) {
            $this->filesystem->remove((string) $identity);
        }
    }

    /**
     * @return SetInterface<Profile>
     */
    public function all(): SetInterface
    {
        return $this
            ->filesystem
            ->all()
            ->reduce(
                Set::of(Profile::class),
                static function(SetInterface $profiles, string $name, File $file): SetInterface {
                    return $profiles->add(
                        \unserialize(
                            (string) $file->content()
                        )
                    );
                }
            );
    }
}
