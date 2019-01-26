<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Server\Web;

use function Innmind\Profiler\Server\{
    Web\bootstrap,
    Domain\bootstrap as domain,
};
use Innmind\OperatingSystem\Factory;
use Innmind\Url\Path;
use Innmind\HttpFramework\RequestHandler;
use Innmind\Templating\Engine;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testInvokation()
    {
        $os = Factory::build();
        $handler = bootstrap(
            $os,
            $this->createMock(Engine::class),
            domain($os->filesystem(), new Path(\sys_get_temp_dir()))
        );

        $this->assertInstanceOf(RequestHandler::class, $handler);
    }
}
