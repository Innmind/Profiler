<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Web;

use function Innmind\Profiler\{
    Web\bootstrap,
    Domain\bootstrap as domain,
};
use Innmind\OperatingSystem\Factory;
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
            domain($os->filesystem(), $os->status()->tmp())
        );

        $this->assertInstanceOf(RequestHandler::class, $handler);
    }
}
