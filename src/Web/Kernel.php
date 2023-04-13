<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Domain\Profile;
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
                'profileRepository',
                fn($_, $os) => new Profile\Repository(
                    $os
                        ->filesystem()
                        ->mount($this->storage),
                ),
            )
            ->service('startProfile', static fn($get, $os) => new Profile\Start(
                $os->clock(),
                $get('profileRepository'),
            ))
            ->service('succeedProfile', static fn($get) => new Profile\Succeed(
                $get('profileRepository'),
            ))
            ->service('listProfiles', static fn($get) => new ListProfiles(
                $get('startProfile'),
                $get('succeedProfile'),
                $get('profileRepository'),
            ))
            ->appendRoutes(
                static fn($routes, $get) => $routes
                    ->add(Route::literal('GET /')->handle(Service::of($get, 'listProfiles'))),
            );
    }
}
