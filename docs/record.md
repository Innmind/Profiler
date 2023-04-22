# Record profiles

By default the profiler record nothing, it's up to you to record the data that will be shown in the profiler.

The examples show how to record data when the profiler is [in the current app](in-app.md).

```php
use Innmind\Framework\{
    Application,
    Middleware,
    Http\RequestHandler,
};
use Innmind\Profiler\{
    Web\Kernel,
    Profiler,
};
use Innmind\Http\Message\{
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
            ->mapRequestHandler(
                static fn($handler, $get) => new class($handler, $get('innmind/profiler')) implements RequestHandler {
                    public function __construct(
                        private RequestHandler $inner,
                        private Profiler $profiler,
                    ) {
                    }

                    public function __invoke(ServerRequest $request): Response
                    {
                        $profile = $this->profiler->start($request->url()->path()->toString());
                        $this->profiler->mutate(
                            $profile,
                            static function($mutation) {
                                $mutation->sections(); // call any method here to record sections data
                            },
                        );

                        $response = ($this->inner)($request);
                        $this->profiler->mutate(
                            $profile,
                            static fn($mutation) => match ($response->statusCode()->successful()) {
                                true => $mutation->succeed($response->statusCode()->toString()),
                                false => $mutation->fail($response->statusCode()->toString()),
                            },
                        );

                        return $response;
                    }
                },
            );
    }
}
```
