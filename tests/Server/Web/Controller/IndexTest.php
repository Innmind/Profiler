<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Controller;

use Innmind\Profiler\Server\{
    Web\Controller\Index,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile,
};
use Innmind\HttpFramework\Controller;
use Innmind\Http\Message\{
    ServerRequest,
    Response,
};
use Innmind\Filesystem\Adapter\MemoryAdapter;
use Innmind\Templating\{
    Engine,
    Name as  TemplateName,
};
use Innmind\Router\{
    Route,
    Route\Name,
};
use Innmind\Immutable\{
    StreamInterface,
    Map,
    Str,
};
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Controller::class,
            new Index(
                new ProfileRepository(
                    new MemoryAdapter
                ),
                $this->createMock(Engine::class)
            )
        );
    }

    public function testInvokation()
    {
        $index = new Index(
            new ProfileRepository(new MemoryAdapter),
            $engine = $this->createMock(Engine::class)
        );
        $engine
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                new TemplateName('index.html.twig'),
                $this->callback(static function($parameters): bool {
                    return $parameters->contains('profiles') &&
                        $parameters->get('profiles') instanceof StreamInterface &&
                        (string) $parameters->get('profiles')->type() === Profile::class;
                })
            );

        $this->assertInstanceOf(
            Response::class,
            $index(
                $this->createMock(ServerRequest::class),
                Route::of(new Name('index'), Str::of('GET /')),
                Map::of('string', 'string')
            )
        );
    }
}
