<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler;

use Innmind\Profiler\{
    Profile,
    Profile\Id,
};
use Innmind\TimeContinuum\{
    Clock,
    PointInTime,
    Earth\Format\ISO8601,
};
use Innmind\Filesystem\{
    Name,
    Directory,
};
use Innmind\Json\{
    Json,
    Exception\Exception,
};
use Innmind\Immutable\Maybe;

final class Load
{
    private Clock $clock;

    private function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * @return Maybe<Profile>
     */
    public function __invoke(Directory $profile): Maybe
    {
        return $profile
            ->get(Name::of('start.json'))
            ->map(static fn($file) => $file->content()->toString())
            ->flatMap(static function($start) {
                try {
                    return Maybe::just(Json::decode($start));
                } catch (Exception $e) {
                    return Maybe::nothing();
                }
            })
            ->flatMap(function($start) use ($profile) {
                if (!\is_array($start)) {
                    return Maybe::nothing();
                }

                $name = Maybe::of($start['name'] ?? null);
                /** @psalm-suppress MixedArgument */
                $startedAt = Maybe::of($start['startedAt'] ?? null)
                    ->filter(\is_string(...))
                    ->flatMap(
                        fn($pointInTime) => $this->clock->at($pointInTime, new ISO8601),
                    );

                return Maybe::all($name, $startedAt)->map(
                    static fn(string $name, PointInTime $startedAt) => Profile::of(
                        Id::of($profile->name()->toString()),
                        $name,
                        $startedAt,
                    ),
                );
            });
    }

    public static function of(Clock $clock): self
    {
        return new self($clock);
    }
}
