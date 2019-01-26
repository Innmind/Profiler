<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Server\Web;

use Innmind\Profiler\Server\Domain\Entity;
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
    MapInterface,
    Map,
    Str,
};
use function Innmind\Immutable\assertMap;

function bootstrap(
    OperatingSystem $os,
    Engine $render,
    MapInterface $repositories
): RequestHandler {
    assertMap('string', 'object', $repositories, 2);

    $resources = (require __DIR__.'/config/resources.php')($os->clock());

    $gateways = Map::of('string', GatewayInterface::class)
        (
            'profile',
            new Gateway\Profile(
                new Gateway\Profile\Create($repositories->get(Entity\Profile::class)),
                new Gateway\Profile\Update($repositories->get(Entity\Profile::class))
            )
        )
        (
            'request_response',
            new Gateway\RequestResponse(
                new Gateway\RequestResponse\Create($repositories->get(Entity\RequestResponse::class)),
                new Gateway\RequestResponse\Update($repositories->get(Entity\RequestResponse::class)),
                new Gateway\RequestResponse\Link($repositories->get(Entity\Profile::class))
            )
        )
        (
            'exception',
            new Gateway\Exception(
                new Gateway\Exception\Create($repositories->get(Entity\Exception::class)),
                new Gateway\Exception\Link($repositories->get(Entity\Profile::class))
            )
        )
        (
            'exception',
            new Gateway\AppGraph(
                new Gateway\AppGraph\Create($repositories->get(Entity\AppGraph::class)),
                new Gateway\AppGraph\Link($repositories->get(Entity\Profile::class))
            )
        );

    $framework = framework();
    $rest = $framework['bridge']['rest_server'](
        $gateways,
        $resources,
        Route::of(
            new Route\Name('capabilities'),
            Str::of('OPTIONS /\*')
        )
    );
    $routes = $rest['routes'];
    $controllers = $rest['controllers'];

    $routes = $routes->add(Route::of(
        new Route\Name('index'),
        Str::of('GET /')
    ));
    $controllers = $controllers->put(
        'index',
        new Controller\Index(
            $repositories->get(Entity\Profile::class),
            $render
        )
    );

    return new Router(
        new RequestMatcher($routes),
        $controllers
    );
}
