<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\{
    Profiler,
    Profile\Id,
    Template\Profile,
};
use Innmind\Router\Route\Variables;
use Innmind\Http\Message\{
    ServerRequest,
    Response\Response,
    StatusCode,
};

final class ShowProfile
{
    private Profiler $profiler;
    private Profile $template;

    public function __construct(
        Profiler $profiler,
        Profile $template,
    ) {
        $this->profiler = $profiler;
        $this->template = $template;
    }

    public function __invoke(ServerRequest $request, Variables $variables): Response
    {
        return $variables
            ->maybe('id')
            ->flatMap(Id::maybe(...))
            ->flatMap($this->profiler->get(...))
            ->match(
                fn($profile) => new Response(
                    StatusCode::ok,
                    $request->protocolVersion(),
                    null,
                    ($this->template)($profile),
                ),
                static fn() => new Response(
                    StatusCode::notFound,
                    $request->protocolVersion(),
                ),
            );
    }
}
