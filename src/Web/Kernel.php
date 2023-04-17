<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Profiler;
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
                ),
            )
            ->service('listProfiles', static fn($get) => new ListProfiles(
                $get('profiler'),
            ))
            ->appendRoutes(
                static fn($routes, $get) => $routes
                    ->add(Route::literal('GET /')->handle(Service::of($get, 'listProfiles'))),
            );
    }
}
