<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway\AppGraph;

use Innmind\Profiler\Server\{
    Web\Gateway\AppGraph\Create,
    Domain\Repository\AppGraphRepository,
};
use Innmind\Rest\Server\{
    ResourceCreator,
    HttpResource\HttpResource,
    HttpResource\Property,
};
use Innmind\Filesystem\Adapter\MemoryAdapter;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceCreator::class,
            new Create(
                new AppGraphRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new AppGraphRepository(
                $adapter = new MemoryAdapter
            )
        );
        $directory = (require 'src/Server/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->child('section')->definition('app_graph'),
            HttpResource::of(
                $directory->child('section')->definition('app_graph'),
                new Property('graph', 'foo')
            )
        );

        $this->assertSame($adapter->all()->key(), (string) $identity);
    }
}
