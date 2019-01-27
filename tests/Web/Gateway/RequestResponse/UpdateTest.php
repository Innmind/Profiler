<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway\RequestResponse;

use Innmind\Profiler\{
    Web\Gateway\RequestResponse\Update,
    Domain\Repository\RequestResponseRepository,
    Domain\Entity\RequestResponse,
    Domain\Entity\Section,
};
use Innmind\Rest\Server\{
    ResourceUpdater,
    HttpResource\HttpResource,
    HttpResource\Property,
    Identity\Identity,
};
use Innmind\Filesystem\Adapter\MemoryAdapter;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceUpdater::class,
            new Update(
                new RequestResponseRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $update = new Update(
            $repository = new RequestResponseRepository(
                new MemoryAdapter
            )
        );
        $directory = (require 'src/Web/config/resources.php')($clock);
        $section = RequestResponse::received(
            Section\Identity::generate('request_response'),
            'foo'
        );
        $repository->add($section);

        $update(
            $directory->child('section')->definition('request_response'),
            new Identity((string) $section->identity()),
            HttpResource::of(
                $directory->child('section')->definition('request_response'),
                new Property('response', 'bar')
            )
        );

        $section = $repository->get($section->identity());
        $this->assertSame('bar', $section->response());
    }
}
