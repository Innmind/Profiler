<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway\Http;

use Innmind\Profiler\{
    Web\Gateway\Http\Update,
    Domain\Repository\SectionRepository,
    Domain\Entity\Http,
    Domain\Entity\Section,
};
use Innmind\Rest\Server\{
    ResourceUpdater,
    HttpResource\HttpResource,
    HttpResource\Property,
    Identity\Identity,
};
use Innmind\Filesystem\Adapter\InMemory;
use Innmind\TimeContinuum\Earth\Clock as Earth;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceUpdater::class,
            new Update(
                new SectionRepository(
                    new InMemory
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $update = new Update(
            $repository = new SectionRepository(
                new InMemory
            )
        );
        $directory = (require 'src/Web/config/resources.php')($clock);
        $section = Http::received(
            Section\Identity::generate(Http::class),
            'foo'
        );
        $repository->add($section);

        $update(
            $directory->child('section')->definition('http'),
            new Identity((string) $section->identity()),
            HttpResource::of(
                $directory->child('section')->definition('http'),
                new Property('response', 'bar')
            )
        );

        $section = $repository->get($section->identity());
        $this->assertSame('bar', $section->response());
    }
}
