<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\Domain\Profile;
use Innmind\Filesystem\File\Content;
use Innmind\Http\Message\{
    ServerRequest,
    Response\Response,
    StatusCode,
};
use Innmind\Immutable\Str;

final class ListProfiles
{
    private Profile\Start $start;
    private Profile\Succeed $succeed;
    private Profile\Repository $repository;

    public function __construct(
        Profile\Start $start,
        Profile\Succeed $succeed,
        Profile\Repository $repository,
    ) {
        $this->start = $start;
        $this->succeed = $succeed;
        $this->repository = $repository;
    }

    public function __invoke(ServerRequest $request): Response
    {
        $profile = ($this->start)(\sprintf(
            '%s %s',
            $request->method()->toString(),
            $request->url()->path()->toString(),
        ));
        ($this->succeed)($profile->id(), StatusCode::ok->toString());

        return new Response(
            StatusCode::ok,
            $request->protocolVersion(),
            null,
            Content\Lines::of(
                $this
                    ->repository
                    ->all()
                    ->map(static fn($profile) => $profile->id()->toString())
                    ->map(Str::of(...))
                    ->map(Content\Line::of(...)),
            ),
        );
    }
}
