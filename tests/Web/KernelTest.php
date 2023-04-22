<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web;

use Innmind\Profiler\{
    Web\Kernel,
    Profiler,
};
use Innmind\Framework\{
    Application,
    Middleware,
    Environment,
    Http\RequestHandler,
};
use Innmind\OperatingSystem\Factory;
use Innmind\Filesystem\{
    Adapter\Filesystem,
    File\Content,
};
use Innmind\Http\{
    Message\ServerRequest,
    Message\Response,
    Message\Method,
    Message\StatusCode,
    ProtocolVersion,
};
use Innmind\Url\{
    Url,
    Path,
};
use Innmind\Html\{
    Reader\Reader,
    Visitor\Element,
    Visitor\Elements,
};
use Innmind\Immutable\{
    Set,
    Map,
};
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    private Path $storage;

    public function setUp(): void
    {
        $this->storage = Path::of(\sys_get_temp_dir().'/innmind_profiler/');
    }

    public function tearDown(): void
    {
        $storage = Filesystem::mount($this->storage);
        $storage->root()->files()->foreach(
            static fn($file) => $storage->remove($file->name()),
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Middleware::class, Kernel::standalone(Path::of('/tmp/')));
        $this->assertInstanceOf(Middleware::class, Kernel::inApp(Path::of('/tmp/')));
    }

    public function testList()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRequestHandler(static fn($handler, $get) => new class($handler, $get('innmind/profiler')) implements RequestHandler {
                public function __construct(
                    private RequestHandler $inner,
                    private Profiler $profiler,
                ) {
                }

                public function __invoke(ServerRequest $request): Response
                {
                    $profile = $this->profiler->start('test');
                    $this->profiler->mutate(
                        $profile,
                        static function($mutation) {
                            $mutation->succeed('200');
                        },
                    );

                    return ($this->inner)($request);
                }
            });

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $lis = Reader::default()($response->body())
            ->flatMap(Element::of('main'))
            ->match(
                static fn($main) => Elements::of('li')($main),
                static fn() => null,
            );
        $this->assertNotNull($lis);
        $this->assertCount(1, $lis);
        $as = $lis->flatMap(static fn($li) => Element::of('a')($li)->match(
            static fn($a) => Set::of($a),
            static fn() => Set::of(),
        ));
        $this->assertCount(1, $as);
        $a = $as->find(static fn() => true)->match(
            static fn($a) => $a->toString(),
            static fn() => null,
        );
        $this->assertStringContainsString(
            '<a href="/profile/',
            $a,
        );
        $this->assertStringContainsString(
            '] [200] test</a>',
            $a,
        );
    }

    public function testProfileWhenNoSectionRecorded()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRequestHandler(static fn($handler, $get) => new class($handler, $get('innmind/profiler')) implements RequestHandler {
                public function __construct(
                    private RequestHandler $inner,
                    private Profiler $profiler,
                ) {
                }

                public function __invoke(ServerRequest $request): Response
                {
                    $this->profiler->start('test');

                    return ($this->inner)($request);
                }
            });

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::default()($response->body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $response = $app->run(new ServerRequest\ServerRequest(
            $a->href(),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $html = Reader::default()($response->body());
        $anyHeaderLi = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('header'))
            ->flatMap(Element::of('li'))
            ->match(
                static fn($li) => $li,
                static fn() => null,
            );
        $this->assertNull($anyHeaderLi);
        $name = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('code'))
            ->match(
                static fn($name) => $name,
                static fn() => null,
            );
        $this->assertNotNull($name);
        $this->assertStringStartsWith(
            '<code class="name started">',
            $name->toString(),
        );
        $this->assertStringContainsString(
            '] test',
            $name->content(),
        );
        $section = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('section'))
            ->match(
                static fn($section) => $section,
                static fn() => null,
            );
        $this->assertNull($section);
    }

    public function testProfileDisplayOneSectionByDefault()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRequestHandler(static fn($handler, $get) => new class($handler, $get('innmind/profiler')) implements RequestHandler {
                public function __construct(
                    private RequestHandler $inner,
                    private Profiler $profiler,
                ) {
                }

                public function __invoke(ServerRequest $request): Response
                {
                    $profile = $this->profiler->start('test');
                    $this->profiler->mutate(
                        $profile,
                        static function($mutation) {
                            $mutation->sections()->appGraph()->record(Content\Lines::ofContent('<app-graph-svg/>'));
                            $mutation->sections()->exception()->record(Content\Lines::ofContent('<exception-svg/>'));
                        },
                    );

                    return ($this->inner)($request);
                }
            });

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::default()($response->body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $response = $app->run(new ServerRequest\ServerRequest(
            $a->href(),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $html = Reader::default()($response->body());
        $lis = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('header'))
            ->match(
                static fn($header) => Elements::of('li')($header),
                static fn() => null,
            );
        $this->assertCount(2, $lis);
        $this->assertSame(
            ['Exception', 'App graph'],
            $lis
                ->flatMap(Elements::of('a'))
                ->map(static fn($a) => $a->content())
                ->toList(),
        );
        $name = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('code'))
            ->match(
                static fn($name) => $name,
                static fn() => null,
            );
        $this->assertNotNull($name);
        $this->assertStringStartsWith(
            '<code class="name started">',
            $name->toString(),
        );
        $this->assertStringContainsString(
            '] test',
            $name->content(),
        );
        $section = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('section'))
            ->match(
                static fn($section) => $section,
                static fn() => null,
            );
        $this->assertNotNull($section);
        $this->assertSame(
            'section-exception',
            $section->attributes()->get('id')->match(
                static fn($attribute) => $attribute->value(),
                static fn() => null,
            ),
        );
        $this->assertStringContainsString(
            '<exception-svg/>',
            $section->toString(),
        );
    }

    public function testSection()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRequestHandler(static fn($handler, $get) => new class($handler, $get('innmind/profiler')) implements RequestHandler {
                public function __construct(
                    private RequestHandler $inner,
                    private Profiler $profiler,
                ) {
                }

                public function __invoke(ServerRequest $request): Response
                {
                    $profile = $this->profiler->start('test');
                    $this->profiler->mutate(
                        $profile,
                        static function($mutation) {
                            $mutation->sections()->appGraph()->record(Content\Lines::ofContent('<app-graph-svg/>'));
                            $mutation->sections()->exception()->record(Content\Lines::ofContent('<exception-svg/>'));
                        },
                    );

                    return ($this->inner)($request);
                }
            });

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::default()($response->body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of($a->href()->toString().'/app-graph'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $html = Reader::default()($response->body());
        $lis = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('header'))
            ->match(
                static fn($header) => Elements::of('li')($header),
                static fn() => null,
            );
        $this->assertCount(2, $lis);
        $this->assertSame(
            ['Exception', 'App graph'],
            $lis
                ->flatMap(Elements::of('a'))
                ->map(static fn($a) => $a->content())
                ->toList(),
        );
        $name = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('code'))
            ->match(
                static fn($name) => $name,
                static fn() => null,
            );
        $this->assertNotNull($name);
        $this->assertStringStartsWith(
            '<code class="name started">',
            $name->toString(),
        );
        $this->assertStringContainsString(
            '] test',
            $name->content(),
        );
        $section = $html
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('section'))
            ->match(
                static fn($section) => $section,
                static fn() => null,
            );
        $this->assertNotNull($section);
        $this->assertSame(
            'section-app-graph',
            $section->attributes()->get('id')->match(
                static fn($attribute) => $attribute->value(),
                static fn() => null,
            ),
        );
        $this->assertStringContainsString(
            '<app-graph-svg/>',
            $section->toString(),
        );
    }

    public function testAllSections()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRequestHandler(static fn($handler, $get) => new class($handler, $get('innmind/profiler')) implements RequestHandler {
                public function __construct(
                    private RequestHandler $inner,
                    private Profiler $profiler,
                ) {
                }

                public function __invoke(ServerRequest $request): Response
                {
                    $profile = $this->profiler->start('test');
                    $this->profiler->mutate(
                        $profile,
                        static function($mutation) {
                            $mutation->sections()->appGraph()->record(Content\Lines::ofContent('<app-graph-svg/>'));
                            $mutation->sections()->callGraph()->record(Content\Lines::ofContent('{"call-graph-svg": []}'));
                            $mutation->sections()->environment()->record(Map::of());
                            $mutation->sections()->exception()->record(Content\Lines::ofContent('<exception-svg/>'));
                            $mutation->sections()->http()->received(Content\Lines::ofContent('request'));
                            $mutation->sections()->http()->respondedWith(Content\Lines::ofContent('response'));
                            $mutation->sections()->processes()->record(Content\Lines::ofContent('process'));
                            $mutation->sections()->remote()->http()->sent(Content\Lines::ofContent('request'));
                            $mutation->sections()->remote()->http()->got(Content\Lines::ofContent('response'));
                            $mutation->sections()->remote()->processes()->record(Content\Lines::ofContent('process'));
                            $mutation->sections()->remote()->sql()->record(Content\Lines::ofContent('sql query'));
                        },
                    );

                    return ($this->inner)($request);
                }
            });

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::default()($response->body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $sections = [];

        foreach ($sections as $section) {
            $response = $app->run(new ServerRequest\ServerRequest(
                Url::of($a->href()->toString().'/'.$section),
                Method::get,
                ProtocolVersion::v11,
            ));

            $this->assertSame(StatusCode::ok, $response->statusCode());
            $this->assertNotEmpty($response->body()->toString());
        }
    }

    public function testInAppList()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::inApp(Path::of(\sys_get_temp_dir().'/innmind_profiler/')));

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::notFound, $response->statusCode());

        $response = $app->run(new ServerRequest\ServerRequest(
            Url::of('/_profiler/'),
            Method::get,
            ProtocolVersion::v11,
        ));

        $this->assertSame(StatusCode::ok, $response->statusCode());
    }
}
