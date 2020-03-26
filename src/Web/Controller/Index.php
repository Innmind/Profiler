<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Controller;

use Innmind\Profiler\Domain\{
    Entity\Profile,
    Repository\ProfileRepository,
};
use Innmind\HttpFramework\Controller;
use Innmind\Http\{
    Message\ServerRequest,
    Message\Response,
    Message\StatusCode,
    Headers,
    Header\ContentType,
    Header\ContentTypeValue,
};
use Innmind\Router\Route;
use Innmind\Templating\{
    Engine,
    Name,
};
use Innmind\Immutable\Map;

final class Index implements Controller
{
    private ProfileRepository $repository;
    private Engine $render;

    public function __construct(
        ProfileRepository $repository,
        Engine $render
    ) {
        $this->repository = $repository;
        $this->render = $render;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequest $request,
        Route $route,
        Map $arguments
    ): Response {
        $profiles = $this
            ->repository
            ->all()
            ->sort(static function(Profile $a, Profile $b): int {
                return (int) $b->startedAt()->aheadOf($a->startedAt());
            });

        return new Response\Response(
            $code = StatusCode::of('OK'),
            $code->associatedReasonPhrase(),
            $request->protocolVersion(),
            Headers::of(
                new ContentType(
                    new ContentTypeValue('text', 'html')
                )
            ),
            ($this->render)(
                new Name('index.html.twig'),
                Map::of('string', 'mixed')
                    ('profiles', $profiles)
            )
        );
    }
}
