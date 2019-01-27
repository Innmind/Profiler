<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Controller;

use Innmind\Profiler\{
    Web\Controller\Profile,
    Domain\Repository\ProfileRepository,
    Domain\Entity\Profile as ProfileEntity,
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
use Innmind\TimeContinuum\PointInTime\Earth\Now;
use Innmind\Immutable\{
    SetInterface,
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
                    new MemoryAdapter
                ),
                Map::of('string', 'object'),
                $this->createMock(Engine::class)
            )
        );
    }

    public function testInvokation()
    {
        $profile = new Profile(
            $repository = new ProfileRepository(new MemoryAdapter),
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
                        $parameters->get('sections') instanceof SetInterface &&
                        (string) $parameters->get('sections')->type() === 'object';
                })
            );
        $repository->add(ProfileEntity::start(
            $identity = ProfileEntity\Identity::generate(),
            'foo',
            new Now
        ));

        $this->assertInstanceOf(
            Response::class,
            $profile(
                $this->createMock(ServerRequest::class),
                Route::of(new Name('index'), Str::of('GET /')),
                Map::of('string', 'string')
                    ('identity', (string) $identity)
            )
        );
    }
}
