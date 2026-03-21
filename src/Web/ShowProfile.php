<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web;

use Innmind\Profiler\{
    Profiler,
    Profile\Id,
    Template\Profile,
};
use Innmind\Http\{
    ServerRequest,
    Response,
    Response\StatusCode,
};
use Innmind\Immutable\{
    Attempt,
    Maybe,
};

/**
 * @internal
 */
final class ShowProfile
{
    public function __construct(
        private Profiler $profiler,
        private Profile $template,
    ) {
    }

    /**
     * @return Attempt<Response>
     */
    public function __invoke(
        ServerRequest $request,
        string $id,
        ?string $section = null,
    ): Attempt {
        $response = Id::maybe($id)
            ->flatMap($this->profiler->get(...))
            ->match(
                fn($profile) => Response::of(
                    StatusCode::ok,
                    $request->protocolVersion(),
                    null,
                    ($this->template)(
                        $profile,
                        Maybe::of($section)->otherwise(
                            static fn() => $profile
                                ->sections()
                                ->first()
                                ->map(static fn($section) => $section->slug()),
                        ),
                    ),
                ),
                static fn() => Response::of(
                    StatusCode::notFound,
                    $request->protocolVersion(),
                ),
            );

        return Attempt::result($response);
    }
}
