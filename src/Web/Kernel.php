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
    Http\Service,
};
use Innmind\Router\Route;
use Innmind\Url\Path;

/**
 * @psalm-suppress ArgumentTypeCoercion
 */
final class Kernel implements Middleware
{
    private Path $storage;
    private Route $list;
    private Route $profile;
    private Route $section;

    private function __construct(
        Path $storage,
        Route $list,
        Route $profile,
        Route $section,
    ) {
        $this->storage = $storage;
        $this->list = $list;
        $this->profile = $profile;
        $this->section = $section;
    }

    public function __invoke(Application $app): Application
    {
        return $app
            ->service(
                'innmind/profiler',
                fn($_, $os) => Profiler::of(
                    $os
                        ->filesystem()
                        ->mount($this->storage),
                    $os->clock(),
                    Load::of($os->clock()),
                ),
            )
            ->service('innmind/profiler.listProfiles', static fn($get) => new ListProfiles(
                $get('innmind/profiler'),
                new Index,
            ))
            ->service('innmind/profiler.showProfile', static fn($get) => new ShowProfile(
                $get('innmind/profiler'),
                new Profile,
            ))
            ->appendRoutes(
                fn($routes, $get) => $routes
                    ->add($this->list->handle(Service::of($get, 'innmind/profiler.listProfiles')))
                    ->add($this->profile->handle(Service::of($get, 'innmind/profiler.showProfile')))
                    ->add($this->section->handle(Service::of($get, 'innmind/profiler.showProfile'))),
            );
    }

    public static function standalone(Path $storage): self
    {
        return new self(
            $storage,
            Route::literal('GET /'),
            Route::literal('GET /profile/{id}'),
            Route::literal('GET /profile/{id}/{section}'),
        );
    }

    public static function inApp(Path $storage): self
    {
        return new self(
            $storage,
            Route::literal('GET /_profiler/'),
            Route::literal('GET /_profiler/profile/{id}'),
            Route::literal('GET /_profiler/profile/{id}/{section}'),
        );
    }
}
