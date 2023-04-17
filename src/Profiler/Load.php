<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profiler;

use Innmind\Profiler\{
    Profile,
    Profile\Id,
    Profile\Status,
    Profile\Section,
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
use Innmind\Immutable\{
    Maybe,
    Sequence,
};

final class Load
{
    private Clock $clock;
    /** @var Sequence<callable(Directory): Maybe<Section>> */
    private Sequence $sections;

    private function __construct(Clock $clock)
    {
        $this->clock = $clock;
        $this->sections = Sequence::lazyStartingWith(
            new Load\Exception,
            new Load\Http,
            new Load\Environment,
            new Load\RawList('Processes', 'processes'),
            new Load\RawList('Remote/Http', 'remote-http'),
            new Load\RawList('Remote/Sql', 'remote-sql'),
            new Load\RawList('Remote/Processes', 'remote-processes'),
            new Load\CallGraph,
            new Load\AppGraph,
        );
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

                $id = Id::maybe($raw->name()->toString());
                $name = Maybe::of($start['name'] ?? null);
                /** @psalm-suppress MixedArgument */
                $startedAt = Maybe::of($start['startedAt'] ?? null)
                    ->filter(\is_string(...))
                    ->flatMap(
                        fn($pointInTime) => $this->clock->at($pointInTime, new ISO8601),
                    );

                return Maybe::all($id, $name, $startedAt)->map(
                    Profile::of(...),
                );
            })
            ->map(fn($profile) => $this->exit($profile, $raw))
            ->map(fn($profile) => $profile->withSections($this->sections->flatMap(
                static fn($load) => $load($raw)->match(
                    static fn($section) => Sequence::of($section),
                    static fn() => Sequence::of(),
                ),
            )));
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
