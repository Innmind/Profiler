<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway\Exception;

use Innmind\Profiler\Server\{
    Web\Gateway\Exception\Create,
    Domain\Repository\ExceptionRepository,
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
                new ExceptionRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new ExceptionRepository(
                $adapter = new MemoryAdapter
            )
        );
        $directory = (require 'src/Server/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->child('section')->definition('exception'),
            HttpResource::of(
                $directory->child('section')->definition('exception'),
                new Property('graph', 'foo')
            )
        );

        $this->assertSame($adapter->all()->key(), (string) $identity);
    }
}
