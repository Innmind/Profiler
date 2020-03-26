<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Domain\Entity;
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Rest\Server\Gateway as GatewayInterface;
use function Innmind\HttpFramework\bootstrap as framework;
use Innmind\HttpFramework\{
    RequestHandler,
    Router,
};
use Innmind\Router\{
    Route,
    RequestMatcher\RequestMatcher,
};
use Innmind\Templating\Engine;
use Innmind\Immutable\{
    Map,
    Str,
};
use function Innmind\Immutable\assertMap;

function bootstrap(
    OperatingSystem $os,
    Engine $render,
    Map $repositories
): RequestHandler {
    assertMap('string', 'object', $repositories, 2);

    $resources = (require __DIR__.'/config/resources.php')($os->clock());

    $gateways = Map::of('string', GatewayInterface::class)
        (
            Entity\Profile::class,
            new Gateway\Profile(
                new Gateway\Profile\Create($repositories->get(Entity\Profile::class)),
                new Gateway\Profile\Update($repositories->get(Entity\Profile::class)),
            ),
        )
        (
            Entity\Http::class,
            new Gateway\Http(
                new Gateway\Http\Create(
                    $repositories->get(Entity\Http::class),
                    $repositories->get(Entity\Profile::class),
                ),
                new Gateway\Http\Update($repositories->get(Entity\Http::class)),
            ),
        )
        (
            Entity\Remote\Http::class,
            new Gateway\Http(
                new Gateway\Remote\Http\Create(
                    $repositories->get(Entity\Remote\Http::class),
                    $repositories->get(Entity\Profile::class),
                ),
                new Gateway\Remote\Http\Update($repositories->get(Entity\Remote\Http::class)),
            ),
        )
        (
            Entity\Remote\Processes::class,
            new Gateway\Processes(
                new Gateway\Processes\Create(
                    $repositories->get(Entity\Remote\Processes::class),
                    $repositories->get(Entity\Profile::class),
                    Entity\Remote\Processes::class,
                ),
            ),
        )
        (
            Entity\Exception::class,
            new Gateway\Exception(
                new Gateway\Exception\Create(
                    $repositories->get(Entity\Exception::class),
                    $repositories->get(Entity\Profile::class),
                ),
            ),
        )
        (
            Entity\AppGraph::class,
            new Gateway\AppGraph(
                new Gateway\AppGraph\Create(
                    $repositories->get(Entity\AppGraph::class),
                    $repositories->get(Entity\Profile::class),
                ),
            ),
        )
        (
            Entity\CallGraph::class,
            new Gateway\CallGraph(
                new Gateway\CallGraph\Create(
                    $repositories->get(Entity\CallGraph::class),
                    $repositories->get(Entity\Profile::class),
                ),
            ),
        )
        (
            Entity\Environment::class,
            new Gateway\Environment(
                new Gateway\Environment\Create(
                    $repositories->get(Entity\Environment::class),
                    $repositories->get(Entity\Profile::class),
                ),
            ),
        )
        (
            Entity\Processes::class,
            new Gateway\Processes(
                new Gateway\Processes\Create(
                    $repositories->get(Entity\Processes::class),
                    $repositories->get(Entity\Profile::class),
                ),
            ),
        );

    $framework = framework();
    $rest = $framework['bridge']['rest_server'](
        $gateways,
        $resources,
        Route::of(
            new Route\Name('capabilities'),
            Str::of('OPTIONS /\*'),
        ),
    );
    $routes = $rest['routes'];
    $controllers = $rest['controllers'];

    $routes = $routes
        ->add(Route::of(
            new Route\Name('index'),
            Str::of('GET /'),
        ))
        ->add(Route::of(
            new Route\Name('profile'),
            Str::of('GET /profile/{identity}'),
        ));
    $controllers = $controllers
        (
            'index',
            new Controller\Index(
                $repositories->get(Entity\Profile::class),
                $render,
            ),
        )
        (
            'profile',
            new Controller\Profile(
                $repositories->get(Entity\Profile::class),
                $repositories,
                $render,
            ),
        );

    return new Router(
        new RequestMatcher($routes),
        $controllers,
    );
}
