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
use function Innmind\Profiler\Server\{
    Domain\bootstrap as domain,
    Web\bootstrap as web,
};

new class extends Main {
    protected function main(ServerRequest $request, OperatingSystem $os): Response
    {
        $domain = domain(
            $os->filesystem(),
            new Path(__DIR__.'/../var')
        );

        $handle = web($os, $domain);

        return $handle($request);
    }
};
