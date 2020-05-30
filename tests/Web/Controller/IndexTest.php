<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Controller;

use Innmind\Profiler\{
    Web\Controller\Index,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile,
};
use Innmind\HttpFramework\Controller;
use Innmind\Http\{
    Message\ServerRequest,
    Message\Response,
    ProtocolVersion,
};
use Innmind\Filesystem\Adapter\InMemory;
use Innmind\Templating\{
    Engine,
    Name as  TemplateName,
};
use Innmind\Router\{
    Route,
    Route\Name,
};
use Innmind\Immutable\{
    Sequence,
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
                    new InMemory
                ),
                $this->createMock(Engine::class)
            )
        );
    }

    public function testInvokation()
    {
        $index = new Index(
            new ProfileRepository(new InMemory),
            $engine = $this->createMock(Engine::class)
        );
        $engine
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                new TemplateName('index.html.twig'),
                $this->callback(static function($parameters): bool {
                    return $parameters->contains('profiles') &&
                        \is_array($parameters->get('profiles'));
                })
            );
        $request = $this->createMock(ServerRequest::class);
        $request
            ->expects($this->any())
            ->method('protocolVersion')
            ->willReturn(new ProtocolVersion(2, 0));

        $this->assertInstanceOf(
            Response::class,
            $index(
                $request,
                Route::of(new Name('index'), Str::of('GET /')),
                Map::of('string', 'string')
            )
        );
    }
}
