<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web\Gateway\Profile;

use Innmind\Profiler\Server\{
    Web\Gateway\Profile\Create,
    Domain\Repository\ProfileRepository,
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
                new ProfileRepository(
                    new MemoryAdapter
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new ProfileRepository(
                $adapter = new MemoryAdapter
            )
        );
        $directory = (require 'src/Server/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->definition('profile'),
            HttpResource::of(
                $directory->definition('profile'),
                new Property('name', 'foo'),
                new Property('started_at', $clock->now())
            )
        );

        $this->assertSame($adapter->all()->key(), (string) $identity);
    }
}
