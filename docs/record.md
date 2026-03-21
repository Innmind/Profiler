# Record profiles

By default the profiler record nothing, it's up to you to record the data that will be shown in the profiler.

The examples show how to record data when the profiler is [in the current app](in-app.md).

```php
use Innmind\Framework\{
    Application,
    Middleware,
};
use Innmind\Profiler\{
    Web\Kernel,
    Web\Services,
    Profiler,
    Profiler\Mutation,
    Profile\Id,
};
use Innmind\DI\Container;
use Innmind\Route\Component;
use Innmind\Http\{
    ServerRequest,
    Response,
};
use Innmind\Url\Path;

final class YourApp implements Middleware
{
    public function __invoke(Application $app): Application
    {
        return $app
            ->map(Kernel::inApp(Path::of('/tmp/')))
            ->mapRoute(
                static fn(
                    Component $route,
                    Container $get,
                ) => Component::of(static function(ServerRequest $request, mixed $input) use ($route, $get) {
                    $profiler = $get(Services::profiler());

                    return $profiler
                        ->start($request->url()->path()->toString())
                        ->flatMap(
                            static fn(Id $profile) => $profiler
                                ->mutate(
                                    $profile,
                                    static fn(Mutation $mutation) => $mutation
                                        ->http()
                                        ->received($request->body()),
                                )
                                ->flatMap(static fn() => $route($request, $input))
                                ->flatMap(
                                    static fn(Response $response) => $profiler
                                        ->mutate(
                                            $profile,
                                            static fn(Mutation $mutation) => match ($response->statusCode()->successful()) {
                                                true => $mutation->succeed($response->statusCode()->toString()),
                                                false => $mutation->fail($response->statusCode()->toString()),
                                            },
                                        )
                                        ->map(static fn() => $response),
                                ),
                        );
                }),
            );
    }
}
```
