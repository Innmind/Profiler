<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Domain;

use function Innmind\Profiler\Server\Domain\bootstrap;
use Innmind\Profiler\Server\Domain\Entity;
use Innmind\OperatingSystem\Filesystem;
use Innmind\Url\Path;
use Innmind\Immutable\MapInterface;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testInterface()
    {
        $repositories = bootstrap(
            $this->createMock(Filesystem::class),
            new Path(\sys_get_temp_dir())
        );

        $this->assertInstanceOf(MapInterface::class, $repositories);
        $this->assertSame('string', (string) $repositories->keyType());
        $this->assertSame('object', (string) $repositories->valueType());
        $this->assertCount(6, $repositories);
        $this->assertSame(
            [
                Entity\Profile::class,
                Entity\AppGraph::class,
                Entity\CommandBus::class,
                Entity\EventBus::class,
                Entity\Exception::class,
                Entity\RequestResponse::class,
            ],
            $repositories->keys()->toPrimitive()
        );
    }
}
