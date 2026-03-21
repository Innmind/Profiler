<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web;

use Innmind\Profiler\{
    Web\Kernel,
    Web\Services,
};
use Innmind\Framework\{
    Application,
    Middleware,
    Environment,
};
use Innmind\Router\Component;
use Innmind\OperatingSystem\Factory;
use Innmind\Filesystem\{
    Adapter,
    File\Content,
    Recover,
};
use Innmind\Http\{
    ServerRequest,
    Method,
    Response\StatusCode,
    ProtocolVersion,
};
use Innmind\Url\{
    Url,
    Path,
};
use Innmind\Html\{
    Reader,
    Visitor\Element,
    Visitor\Elements,
};
use Innmind\Immutable\Map;
use Innmind\BlackBox\PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    private Path $storage;

    public function setUp(): void
    {
        $this->storage = Path::of(\sys_get_temp_dir().'/innmind_profiler/');
    }

    public function tearDown(): void
    {
        $storage = Adapter::mount($this->storage)
            ->recover(Recover::mount(...))
            ->unwrap();
        $_ = $storage->root()->all()->foreach(
            static fn($file) => $storage->remove($file->name())->unwrap(),
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
            ->mapRoute(
                static fn($route, $get) => Component::of(static function(
                    $request,
                    $input,
                ) use ($get) {
                    $profiler = $get(Services::profiler);
                    $profile = $profiler->start('test')->unwrap();

                    return $profiler
                        ->mutate(
                            $profile,
                            static fn($mutation) => $mutation->succeed('200'),
                        )
                        ->map(static fn() => $input);
                })->pipe($route),
            );

        $response = $app->run(ServerRequest::of(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $lis = Reader::new()($response->body())
            ->maybe()
            ->flatMap(Element::of('main'))
            ->match(
                static fn($main) => Elements::of('li')($main),
                static fn() => null,
            );
        $this->assertNotNull($lis);
        $this->assertSame(1, $lis->size());
        $as = $lis->flatMap(
            static fn($li) => Element::of('a')($li)->toSequence(),
        );
        $this->assertSame(1, $as->size());
        $a = $as->first()->match(
            static fn($a) => $a->normalize()->asContent()->toString(),
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
            ->mapRoute(
                static fn($route, $get) => Component::of(static function(
                    $request,
                    $input,
                ) use ($get) {
                    $profiler = $get(Services::profiler);

                    return $profiler
                        ->start('test')
                        ->map(static fn() => $input);
                })->pipe($route),
            );

        $response = $app->run(ServerRequest::of(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::new()($response->body())
            ->maybe()
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $response = $app->run(ServerRequest::of(
            $a->href(),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $html = Reader::new()($response->body());
        $anyHeaderLi = $html
            ->maybe()
            ->flatMap(Element::body())
            ->flatMap(Element::of('header'))
            ->flatMap(Element::of('li'))
            ->match(
                static fn($li) => $li,
                static fn() => null,
            );
        $this->assertNull($anyHeaderLi);
        $name = $html
            ->maybe()
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('code'))
            ->match(
                static fn($name) => $name->asContent()->toString(),
                static fn() => null,
            );
        $this->assertNotNull($name);
        $this->assertStringStartsWith(
            '<code class="name started">',
            $name,
        );
        $this->assertStringContainsString(
            '] test',
            $name,
        );
        $section = $html
            ->maybe()
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
            ->mapRoute(
                static fn($route, $get) => Component::of(static function(
                    $request,
                    $input,
                ) use ($get) {
                    $profiler = $get(Services::profiler);

                    return $profiler
                        ->start('test')
                        ->flatMap(static fn($profile) => $profiler->mutate(
                            $profile,
                            static fn($mutation) => $mutation
                                ->sections()
                                ->appGraph()
                                ->record(Content::ofString('<app-graph-svg/>'))
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->exception()
                                        ->record(Content::ofString('<exception-svg/>')),
                                ),
                        ))
                        ->map(static fn() => $input);
                })->pipe($route),
            );

        $response = $app->run(ServerRequest::of(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::new()($response->body())
            ->maybe()
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $response = $app->run(ServerRequest::of(
            $a->href(),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $html = Reader::new()($response->body());
        $lis = $html
            ->maybe()
            ->flatMap(Element::body())
            ->flatMap(Element::of('header'))
            ->match(
                static fn($header) => Elements::of('li')($header),
                static fn() => null,
            );
        $this->assertSame(2, $lis->size());
        $this->assertSame(
            ['Exception', 'App graph'],
            $lis
                ->flatMap(Elements::of('a'))
                ->flatMap(static fn($a) => $a->normalize()->children())
                ->map(static fn($a) => $a->content())
                ->toList(),
        );
        $name = $html
            ->maybe()
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('code'))
            ->match(
                static fn($name) => $name->asContent()->toString(),
                static fn() => null,
            );
        $this->assertNotNull($name);
        $this->assertStringStartsWith(
            '<code class="name started">',
            $name,
        );
        $this->assertStringContainsString(
            '] test',
            $name,
        );
        $section = $html
            ->maybe()
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
            $section->attribute('id')->match(
                static fn($attribute) => $attribute->value(),
                static fn() => null,
            ),
        );
        $this->assertStringContainsString(
            '<exception-svg/>',
            $section->asContent()->toString(),
        );
    }

    public function testSection()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRoute(
                static fn($route, $get) => Component::of(static function(
                    $request,
                    $input,
                ) use ($get) {
                    $profiler = $get(Services::profiler);

                    return $profiler
                        ->start('test')
                        ->flatMap(static fn($profile) => $profiler->mutate(
                            $profile,
                            static fn($mutation) => $mutation
                                ->sections()
                                ->appGraph()
                                ->record(Content::ofString('<app-graph-svg/>'))
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->exception()
                                        ->record(Content::ofString('<exception-svg/>')),
                                ),
                        ))
                        ->map(static fn() => $input);
                })->pipe($route),
            );

        $response = $app->run(ServerRequest::of(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::new()($response->body())
            ->maybe()
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $response = $app->run(ServerRequest::of(
            Url::of($a->href()->toString().'/app-graph'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $html = Reader::new()($response->body());
        $lis = $html
            ->maybe()
            ->flatMap(Element::body())
            ->flatMap(Element::of('header'))
            ->match(
                static fn($header) => Elements::of('li')($header),
                static fn() => null,
            );
        $this->assertSame(2, $lis->size());
        $this->assertSame(
            ['Exception', 'App graph'],
            $lis
                ->flatMap(Elements::of('a'))
                ->flatMap(static fn($a) => $a->normalize()->children())
                ->map(static fn($a) => $a->content())
                ->toList(),
        );
        $name = $html
            ->maybe()
            ->flatMap(Element::body())
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('code'))
            ->match(
                static fn($name) => $name->asContent()->toString(),
                static fn() => null,
            );
        $this->assertNotNull($name);
        $this->assertStringStartsWith(
            '<code class="name started">',
            $name,
        );
        $this->assertStringContainsString(
            '] test',
            $name,
        );
        $section = $html
            ->maybe()
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
            $section->attribute('id')->match(
                static fn($attribute) => $attribute->value(),
                static fn() => null,
            ),
        );
        $this->assertStringContainsString(
            '<app-graph-svg/>',
            $section->asContent()->toString(),
        );
    }

    public function testAllSections()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::standalone(Path::of(\sys_get_temp_dir().'/innmind_profiler/')))
            ->mapRoute(
                static fn($route, $get) => Component::of(static function(
                    $request,
                    $input,
                ) use ($get) {
                    $profiler = $get(Services::profiler);

                    return $profiler
                        ->start('test')
                        ->flatMap(static fn($profile) => $profiler->mutate(
                            $profile,
                            static fn($mutation) => $mutation
                                ->sections()
                                ->appGraph()
                                ->record(Content::ofString('<app-graph-svg/>'))
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->callGraph()
                                        ->record(Content::ofString('{"call-graph-svg": []}')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->environment()
                                        ->record(Map::of()),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->exception()
                                        ->record(Content::ofString('<exception-svg/>')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->http()
                                        ->received(Content::ofString('request')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->http()
                                        ->respondedWith(Content::ofString('response')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->processes()
                                        ->record(Content::ofString('process')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->remote()
                                        ->http()
                                        ->sent(Content::ofString('request')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->remote()
                                        ->http()
                                        ->got(Content::ofString('response')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->remote()
                                        ->processes()
                                        ->record(Content::ofString('process')),
                                )
                                ->flatMap(
                                    static fn() => $mutation
                                        ->sections()
                                        ->remote()
                                        ->sql()
                                        ->record(Content::ofString('sql query')),
                                ),
                        ))
                        ->map(static fn() => $input);
                })->pipe($route),
            );

        $response = $app->run(ServerRequest::of(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
        $a = Reader::new()($response->body())
            ->maybe()
            ->flatMap(Element::of('main'))
            ->flatMap(Element::of('li'))
            ->flatMap(Element::of('a'))
            ->match(
                static fn($a) => $a,
                static fn() => null,
            );
        $this->assertNotNull($a);

        $sections = [
            'app-graph',
            'call-graph',
            'environment',
            'exception',
            'http',
            'processes',
            'remote-processes',
            'remote-http',
            'remote-sql',
        ];

        foreach ($sections as $section) {
            $response = $app->run(ServerRequest::of(
                Url::of($a->href()->toString().'/'.$section),
                Method::get,
                ProtocolVersion::v11,
            ))->unwrap();

            $this->assertSame(StatusCode::ok, $response->statusCode());
            $this->assertNotSame('', $response->body()->toString());
        }
    }

    public function testInAppList()
    {
        $os = Factory::build();
        $app = Application::http($os, Environment::test([]))
            ->map(Kernel::inApp(Path::of(\sys_get_temp_dir().'/innmind_profiler/')));

        $response = $app->run(ServerRequest::of(
            Url::of('/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::notFound, $response->statusCode());

        $response = $app->run(ServerRequest::of(
            Url::of('/_profiler/'),
            Method::get,
            ProtocolVersion::v11,
        ))->unwrap();

        $this->assertSame(StatusCode::ok, $response->statusCode());
    }
}
