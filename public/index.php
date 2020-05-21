<?php
declare(strict_types = 1);

require __DIR__.'/../vendor/autoload.php';

use Innmind\HttpFramework\{
    Main,
    Application,
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
    protected function configure(Application $app): Application
    {
        return $app->handler(fn(OperatingSystem $os) => web(
            $os,
            render(
                Path::of(__DIR__.'/../templates/'),
                null,
                Map::of('string', 'object')
                    ('name', new Names)
                    ('render', new Templates),
            ),
            domain(
                $os->filesystem(),
                Path::of(__DIR__.'/../var/'),
            ),
        ));
    }
};
