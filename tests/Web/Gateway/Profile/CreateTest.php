<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web\Gateway\Profile;

use Innmind\Profiler\{
    Web\Gateway\Profile\Create,
    Domain\Repository\ProfileRepository,
};
use Innmind\Rest\Server\{
    ResourceCreator,
    HttpResource\HttpResource,
    HttpResource\Property,
};
use Innmind\Filesystem\Adapter\InMemory;
use Innmind\TimeContinuum\Earth\Clock as Earth;
use function Innmind\Immutable\first;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            ResourceCreator::class,
            new Create(
                new ProfileRepository(
                    new InMemory
                )
            )
        );
    }

    public function testInvokation()
    {
        $clock = new Earth;
        $create = new Create(
            new ProfileRepository(
                $adapter = new InMemory
            )
        );
        $directory = (require 'src/Web/config/resources.php')($clock);

        $identity = $create(
            $directory->definition('profile'),
            HttpResource::of(
                $directory->definition('profile'),
                new Property('name', 'foo'),
                new Property('started_at', $clock->now())
            )
        );

        $this->assertSame(first($adapter->all())->name()->toString(), $identity->toString());
    }
}
