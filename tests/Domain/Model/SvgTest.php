<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Model;

use Innmind\Profiler\Domain\Model\Svg;
use Innmind\Immutable\Str;
use PHPUnit\Framework\TestCase;

class SvgTest extends TestCase
{
    public function testInterface()
    {
        $svg = new Svg('<svg></svg>');

        $this->assertInstanceOf(Str::class, $svg->dataUri());
        $this->assertSame('data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=', (string) $svg->dataUri());
        $this->assertSame('<svg></svg>', (string) $svg);
    }
}
