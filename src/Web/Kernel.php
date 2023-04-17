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

    public function __construct(Path $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(Application $app): Application
    {
        return $app
            ->service(
                'profiler',
                fn($_, $os) => Profiler::of(
                    $os
                        ->filesystem()
                        ->mount($this->storage),
                    $os->clock(),
                    Load::of($os->clock()),
                ),
            )
            ->service('listProfiles', static fn($get) => new ListProfiles(
                $get('profiler'),
                new Index,
            ))
            ->service('showProfile', static fn($get) => new ShowProfile(
                $get('profiler'),
                new Profile,
            ))
            ->appendRoutes(
                static fn($routes, $get) => $routes
                    ->add(Route::literal('GET /')->handle(Service::of($get, 'listProfiles')))
                    ->add(Route::literal('GET /profile/{id}')->handle(Service::of($get, 'showProfile')))
                    ->add(Route::literal('GET /profile/{id}/{section}')->handle(Service::of($get, 'showProfile'))),
            );
    }
}
