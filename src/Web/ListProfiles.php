<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Profiler;
use Innmind\Filesystem\File\Content;
use Innmind\Http\Message\{
    ServerRequest,
    Response\Response,
    StatusCode,
};
use Innmind\Immutable\Str;

final class ListProfiles
{
    private Profiler $profiler;

    public function __construct(
        Profiler $profiler,
    ) {
        $this->profiler = $profiler;
    }

    public function __invoke(ServerRequest $request): Response
    {
        $profile = $this->profiler->start(\sprintf(
            '%s %s',
            $request->method()->toString(),
            $request->url()->path()->toString(),
        ));
        $this->profiler->mutate(
            $profile,
            static fn($mutation) => $mutation->succeed(StatusCode::ok->toString()),
        );

        return new Response(
            StatusCode::ok,
            $request->protocolVersion(),
            null,
            Content\Lines::of(
                $this
                    ->profiler
                    ->all()
                    ->map(static fn($profile) => $profile->toString())
                    ->map(Str::of(...))
                    ->map(Content\Line::of(...)),
            ),
        );
    }
}
