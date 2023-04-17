<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler;

use Innmind\Profiler\{
    Profile,
    Profile\Id,
    Profile\Status,
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
    public function __invoke(Directory $raw): Maybe
    {
        return $raw
            ->get(Name::of('start.json'))
            ->map(static fn($file) => $file->content()->toString())
            ->flatMap(static function($start) {
                try {
                    return Maybe::just(Json::decode($start));
                } catch (Exception $e) {
                    return Maybe::nothing();
                }
            })
            ->flatMap(function($start) use ($raw) {
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
                        Id::of($raw->name()->toString()),
                        $name,
                        $startedAt,
                    ),
                );
            })
            ->map(fn($profile) => $this->exit($profile, $raw));
    }

    public static function of(Clock $clock): self
    {
        return new self($clock);
    }

    private function exit(Profile $profile, Directory $raw): Profile
    {
        return $raw
            ->get(Name::of('exit.json'))
            ->map(static fn($file) => $file->content()->toString())
            ->flatMap(static function($exit) {
                try {
                    return Maybe::just(Json::decode($exit));
                } catch (Exception $e) {
                    return Maybe::nothing();
                }
            })
            ->flatMap(static function($exit) {
                if (!\is_array($exit)) {
                    return Maybe::nothing();
                }

                $message = Maybe::of($exit['message'] ?? null)->filter(\is_string(...));
                $status = Maybe::of($exit['succeeded'] ?? null)->map(static fn($succeeded) => match ($succeeded) {
                    true => Status::succeeded,
                    default => Status::failed,
                });

                return Maybe::all($message, $status)->map(
                    static fn(string $message, Status $status) => [$message, $status],
                );
            })
            ->match(
                static fn($pair) => $profile->withExit($pair[1], $pair[0]),
                static fn() => $profile,
            );
    }
}
