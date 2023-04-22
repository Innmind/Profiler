<?php

declare(strict_types = 1);

require __DIR__.'/../vendor/autoload.php';

use Innmind\Profiler\Web\Kernel;
use Innmind\Framework\{
    Application,
    Main\Http,
};
use Innmind\Url\Path;

new class extends Http {
    protected function configure(Application $app): Application
    {
        return $app->map(Kernel::standalone(Path::of(__DIR__.'/../var/profiles/')));
    }
};
