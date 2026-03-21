<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\{
    Profiler,
    Template\Index,
};
use Innmind\Http\{
    ServerRequest,
    Response,
    Response\StatusCode,
};
use Innmind\Immutable\Attempt;

/**
 * @internal
 */
final class ListProfiles
{
    public function __construct(
        private Profiler $profiler,
        private Index $template,
    ) {
    }

    /**
     * @return Attempt<Response>
     */
    public function __invoke(ServerRequest $request): Attempt
    {
        return Attempt::result(Response::of(
            StatusCode::ok,
            $request->protocolVersion(),
            null,
            ($this->template)($this->profiler->all()),
        ));
    }
}
