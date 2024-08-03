<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\{
    Profiler,
    Profiler\Load,
    Template\Index,
    Template\Profile,
};
use Innmind\Framework\{
    Application,
    Middleware,
    Http\Service,
};
use Innmind\Filesystem\{
    File,
    Name,
};
use Innmind\Http\{
    Response,
    Response\StatusCode,
    Headers,
    Header\ContentType,
    Header\Header,
    Header\Value\Value,
};
use Innmind\Router\Route;
use Innmind\UI\Theme;
use Innmind\Url\Path;
use Innmind\Immutable\Predicate\Instance;

/**
 * @psalm-suppress ArgumentTypeCoercion
 */
final class Kernel implements Middleware
{
    private Path $storage;
    private Route $list;
    private Route $profile;
    private Route $section;
    private Route $style;
    private Route $logo;

    private function __construct(
        Path $storage,
        Route $list,
        Route $profile,
        Route $section,
        Route $style,
        Route $logo,
    ) {
        $this->storage = $storage;
        $this->list = $list;
        $this->profile = $profile;
        $this->section = $section;
        $this->style = $style;
        $this->logo = $logo;
    }

    public function __invoke(Application $app): Application
    {
        return $app
            ->service(
                'innmind/profiler',
                fn($_, $os) => Profiler::of(
                    $os
                        ->filesystem()
                        ->mount($this->storage),
                    $os->clock(),
                    Load::of($os->clock()),
                ),
            )
            ->service('innmind/profiler.listProfiles', fn($get) => new ListProfiles(
                $get('innmind/profiler'),
                new Index(
                    $this->profile->template(),
                    $this->style->template(),
                    $this->logo->template(),
                ),
            ))
            ->service('innmind/profiler.showProfile', fn($get) => new ShowProfile(
                $get('innmind/profiler'),
                new Profile($this->list->template(), $this->section->template()),
            ))
            ->appendRoutes(
                fn($routes, $get, $os) => $routes
                    ->add($this->list->handle(Service::of($get, 'innmind/profiler.listProfiles')))
                    ->add($this->profile->handle(Service::of($get, 'innmind/profiler.showProfile')))
                    ->add($this->section->handle(Service::of($get, 'innmind/profiler.showProfile')))
                    ->add($this->style->handle(
                        static fn($request) => Theme::default->load($os->filesystem())->match(
                            static fn($file) => Response::of(
                                StatusCode::ok,
                                $request->protocolVersion(),
                                Headers::of(
                                    ContentType::of('text', 'css'),
                                ),
                                $file,
                            ),
                            static fn() => Response::of(
                                StatusCode::notFound,
                                $request->protocolVersion(),
                            ),
                        ),
                    ))
                    ->add($this->logo->handle(
                        static fn($request) => $os
                            ->filesystem()
                            ->mount(Path::of(\dirname(__DIR__, 2).'/assets/'))
                            ->get(Name::of('logo.svg'))
                            ->keep(Instance::of(File::class))
                            ->match(
                                static fn($file) => Response::of(
                                    StatusCode::ok,
                                    $request->protocolVersion(),
                                    Headers::of(
                                        new Header('Content-Type', new Value('image/svg+xml')),
                                    ),
                                    $file->content(),
                                ),
                                static fn() => Response::of(
                                    StatusCode::notFound,
                                    $request->protocolVersion(),
                                ),
                            ),
                    )),
            );
    }

    public static function standalone(Path $storage): self
    {
        return new self(
            $storage,
            Route::literal('GET /'),
            Route::literal('GET /profile/{id}'),
            Route::literal('GET /profile/{id}/{section}'),
            Route::literal('GET /style'),
            Route::literal('GET /logo'),
        );
    }

    public static function inApp(Path $storage): self
    {
        return new self(
            $storage,
            Route::literal('GET /_profiler/'),
            Route::literal('GET /_profiler/profile/{id}'),
            Route::literal('GET /_profiler/profile/{id}/{section}'),
            Route::literal('GET /_profiler/style'),
            Route::literal('GET /_profiler/logo'),
        );
    }
}
