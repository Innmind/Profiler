<?php
declare(strict_types = 1);

require __DIR__.'/../vendor/autoload.php';

use Innmind\HttpServer\Main;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Url\Path;
use Innmind\Profiler\Web\{
    Names,
    Templates,
};
use Innmind\Immutable\Map;
use function Innmind\Profiler\{
    Domain\bootstrap as domain,
    Web\bootstrap as web,
};
use function Innmind\Templating\bootstrap as render;

new class extends Main {
    protected function main(ServerRequest $request, OperatingSystem $os): Response
    {
        $domain = domain(
            $os->filesystem(),
            new Path(__DIR__.'/../var')
        );

        $handle = web(
            $os,
            render(
                new Path(__DIR__.'/../templates'),
                null,
                Map::of('string', 'object')
                    ('name', new Names)
                    ('render', new Templates)
            ),
            $domain
        );

        return $handle($request);
    }
};
