<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\{
    Profiler,
    Template\Index,
};
use Innmind\Http\Message\{
    ServerRequest,
    Response\Response,
    StatusCode,
};

final class ListProfiles
{
    private Profiler $profiler;
    private Index $template;

    public function __construct(
        Profiler $profiler,
        Index $template,
    ) {
        $this->profiler = $profiler;
        $this->template = $template;
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
            ($this->template)($this->profiler->all()),
        );
    }
}
