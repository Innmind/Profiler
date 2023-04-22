# Run as standalone app

The first step is to add an entrypoint that will be exposed by an HTTP server.

```php
<?php
declare(strict_types=1);

require 'path/to/vendor/autoload.php';

use Innmind\Framework\{
    Application,
    Main\Http,
};
use Innmind\Profiler\Web\Kernel;
use Innmind\Url\Path;

new class extends Http {
    protected function configure(Application $app): Application
    {
        return $app
            ->map(Kernel::standalone(Path::of('/tmp/')))
            ->map(new YourMiddleware);
    }
}
```

This will expose the profiler via the `/` route.

Because the profiler doesn't come with its own HTTP api to record profiles you will need to implements these yourself via `YourMiddleware`.
