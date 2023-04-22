# Add the profiler to an existing app

For this use cas you must [`innmind/framework`](https://packagist.org/packages/innmind/framework).

```php
use Innmind\Framework\{
    Application,
    Main\Http,
};
use Innmind\Profiler\Web\Kernel;
use Innmind\Url\Path;

new class extends Http {
    protected function configure(Application $app): Application
    {
        return $app->map(Kernel::inApp(Path::of('/tmp/')));
    }
};
```

This example will expose the profiler under the `/_profiler/` route and the profiles will be stored in the `/tmp/` folder. Using the tmp folder means the profiles will be lost when rebooting your machine, you can use a local folder if you want to keep them.

> **Note** this example add the middleware in the entrypoint of your app but you can add it in your own middleware.
