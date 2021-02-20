<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Controller;

use Innmind\Profiler\{
    Web\Controller\Profile,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile as ProfileEntity,
    Domain\Entity\Section,
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
use Innmind\TimeContinuum\Earth\PointInTime\Now;
use Innmind\Immutable\{
    Set,
    Map,
    Str,
};
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Controller::class,
            new Profile(
                new ProfileRepository(
                    new InMemory
                ),
                Map::of('string', 'object'),
                $this->createMock(Engine::class)
            )
        );
    }

    public function testInvokation()
    {
        $profile = new Profile(
            $repository = new ProfileRepository(new InMemory),
            Map::of('string', 'object'),
            $engine = $this->createMock(Engine::class)
        );
        $engine
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                new TemplateName('profile.html.twig'),
                $this->callback(static function($parameters): bool {
                    return $parameters->contains('profile') &&
                        $parameters->get('profile') instanceof ProfileEntity &&
                        $parameters->contains('sections') &&
                        \is_array($parameters->get('sections'));
                })
            );
        $repository->add(ProfileEntity::start(
            $identity = ProfileEntity\Identity::generate(),
            'foo',
            new Now
        ));
        $request = $this->createMock(ServerRequest::class);
        $request
            ->expects($this->any())
            ->method('protocolVersion')
            ->willReturn(new ProtocolVersion(2, 0));

        $this->assertInstanceOf(
            Response::class,
            $profile(
                $request,
                Route::of(new Name('index'), Str::of('GET /')),
                Map::of('string', 'string')
                    ('identity', $identity->toString())
            )
        );
    }
}
