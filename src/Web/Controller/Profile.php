<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Web\Controller;

use Innmind\Profiler\Domain\{
    Entity\Profile as ProfileEntity,
    Entity\Section,
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
use Innmind\Immutable\{
    Map,
    Set,
};
use function Innmind\Immutable\assertMap;

final class Profile implements Controller
{
    private ProfileRepository $repository;
    private Map $repositories;
    private Engine $render;

    public function __construct(
        ProfileRepository $repository,
        Map $repositories,
        Engine $render
    ) {
        assertMap('string', 'object', $repositories, 2);

        $this->repository = $repository;
        $this->repositories = $repositories;
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
        $profile = $this->repository->get(new ProfileEntity\Identity(
            $arguments->get('identity')
        ));
        $sections = $profile->sections()->reduce(
            Set::of(Section::class),
            function(Set $sections, Section\Identity $identity): Set {
                return $sections->add(
                    $this->repositories->get($identity->section())->get($identity)
                );
            }
        );

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
                new Name('profile.html.twig'),
                Map::of('string', 'mixed')
                    ('profile', $profile)
                    ('sections', $sections)
            )
        );
    }
}
