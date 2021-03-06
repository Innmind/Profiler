<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain;

use function Innmind\Profiler\Domain\bootstrap;
use Innmind\Profiler\Domain\Entity;
use Innmind\OperatingSystem\Filesystem;
use Innmind\Url\Path;
use Innmind\Immutable\Map;
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testInterface()
    {
        $repositories = bootstrap(
            $this->createMock(Filesystem::class),
            Path::of(\sys_get_temp_dir().'/')
        );

        $this->assertInstanceOf(Map::class, $repositories);
        $this->assertSame('string', (string) $repositories->keyType());
        $this->assertSame('object', (string) $repositories->valueType());
        $this->assertCount(9, $repositories);
        $this->assertSame(
            [
                Entity\Profile::class,
                Entity\AppGraph::class,
                Entity\CallGraph::class,
                Entity\Exception::class,
                Entity\Http::class,
                Entity\Environment::class,
                Entity\Processes::class,
                Entity\Remote\Http::class,
                Entity\Remote\Processes::class,
            ],
            unwrap($repositories->keys()),
        );
    }
}
