<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway\RequestResponse;

use Innmind\Profiler\Server\{
    Web\Gateway\RequestResponse\Create,
    Domain\Repository\RequestResponseRepository,
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
                new RequestResponseRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new RequestResponseRepository(
                $adapter = new MemoryAdapter
            )
        );
        $directory = (require 'src/Server/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->child('section')->definition('request_response'),
            HttpResource::of(
                $directory->child('section')->definition('request_response'),
                new Property('request', 'foo')
            )
        );

        $this->assertSame($adapter->all()->key(), (string) $identity);
    }
}
