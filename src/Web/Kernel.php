<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\{
    Profiler,
    Profiler\Load,
    Template\Index,
    Template\Profile,
};
use Innmind\Framework\{
    Application,
    Middleware,
    Http\Route,
};
use Innmind\Filesystem\Recover;
use Innmind\UrlTemplate\Template;
use Innmind\Url\Path;

/**
 * @psalm-immutable
 */
final class Kernel implements Middleware
{
    private Path $storage;
    private Template $list;
    private Template $profile;
    private Template $section;

    private function __construct(
        Path $storage,
        Template $list,
        Template $profile,
        Template $section,
    ) {
        $this->storage = $storage;
        $this->list = $list;
        $this->profile = $profile;
        $this->section = $section;
    }

    #[\Override]
    public function __invoke(Application $app): Application
    {
        return $app
            ->service(
                Services::profiler,
                fn($_, $os) => Profiler::of(
                    $os
                        ->filesystem()
                        ->mount($this->storage)
                        ->recover(Recover::mount(...))
                        ->unwrap(),
                    $os->clock(),
                    Load::of($os->clock()),
                ),
            )
            ->service(Services::listProfiles, fn($get) => new ListProfiles(
                $get(Services::profiler()),
                new Index($this->list, $this->profile),
            ))
            ->service(Services::showProfile, fn($get) => new ShowProfile(
                $get(Services::profiler()),
                new Profile($this->list, $this->section),
            ))
            ->route(Route::get(
                $this->list,
                Services::listProfiles(),
            ))
            ->route(Route::get(
                $this->profile,
                Services::showProfile(),
            ))
            ->route(Route::get(
                $this->section,
                Services::showProfile(),
            ));
    }

    /**
     * @psalm-pure
     */
    public static function standalone(Path $storage): self
    {
        return new self(
            $storage,
            Template::of('/'),
            Template::of('/profile/{id}'),
            Template::of('/profile/{id}/{section}'),
        );
    }

    /**
     * @psalm-pure
     */
    public static function inApp(Path $storage): self
    {
        return new self(
            $storage,
            Template::of('/_profiler/'),
            Template::of('/_profiler/profile/{id}'),
            Template::of('/_profiler/profile/{id}/{section}'),
        );
    }
}
