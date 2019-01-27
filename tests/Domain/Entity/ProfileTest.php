<?php
declare(strict_types = 1);

namespace Tests\Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\{
    Entity\Profile,
    Entity\Profile\Identity,
    Entity\Profile\Status,
    Entity\Section\Identity as Section,
    Exception\LogicException,
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    public function testStart()
    {
        $profile = Profile::start(
            $identity = Identity::generate(),
            'foo',
            $startedAt = $this->createMock(PointInTimeInterface::class)
        );

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertSame($identity, $profile->identity());
        $this->assertSame('foo', $profile->name());
        $this->assertFalse($profile->closed());
        $this->assertInstanceOf(SetInterface::class, $profile->sections());
        $this->assertSame(Section::class, (string) $profile->sections()->type());
        $this->assertCount(0, $profile->sections());
        $this->assertSame('[] foo', (string) $profile);
    }

    public function testAdd()
    {
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            $this->createMock(PointInTimeInterface::class)
        );
        $section = Section::generate('section');

        $this->assertNull($profile->add($section));
        $this->assertSame([$section], $profile->sections()->toPrimitive());
    }

    public function testFail()
    {
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            $this->createMock(PointInTimeInterface::class)
        );

        $this->assertNull($profile->fail('bar'));
        $this->assertTrue($profile->closed());
        $this->assertEquals(Status::failed(), $profile->status());
        $this->assertSame('[] [bar] foo', (string) $profile);
    }

    public function testSuceeed()
    {
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            $this->createMock(PointInTimeInterface::class)
        );

        $this->assertNull($profile->succeed('bar'));
        $this->assertTrue($profile->closed());
        $this->assertEquals(Status::succeeded(), $profile->status());
        $this->assertSame('[] [bar] foo', (string) $profile);
    }

    public function testThrowWhenAddingSectionToAClosedProfile()
    {
        $profile = Profile::start(
            Identity::generate(),
            'foo',
            $this->createMock(PointInTimeInterface::class)
        );
        $profile->succeed('bar');

        $this->expectException(LogicException::class);

        $profile->add(Section::generate('section'));
    }
}
