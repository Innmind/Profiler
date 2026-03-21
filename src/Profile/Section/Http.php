<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Xml\{
    Node,
    Element,
    Element\Name,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
};

/**
 * @internal
 * @psalm-immutable
 */
final class Http implements Section
{
    private Content $request;
    /** @var Maybe<Content> */
    private Maybe $response;

    /**
     * @param Maybe<Content> $response
     */
    private function __construct(Content $request, Maybe $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param Maybe<Content> $response
     */
    public static function of(Content $request, Maybe $response): self
    {
        return new self($request, $response);
    }

    #[\Override]
    public function name(): string
    {
        return 'Http';
    }

    #[\Override]
    public function slug(): string
    {
        return 'http';
    }

    #[\Override]
    public function render(): Element
    {
        return Element::of(
            Name::of('div'),
            null,
            $this->response->match(
                fn($response) => Sequence::of($this->wrap($this->request), $this->wrap($response)),
                fn() => Sequence::of($this->wrap($this->request)),
            ),
        );
    }

    private function wrap(Content $content): Element
    {
        return Element::of(
            Name::of('code'),
            null,
            $content
                ->lines()
                ->map(static fn($line) => $line->toString())
                ->map(\htmlspecialchars(...))
                ->map(Node::text(...))
                ->flatMap(static fn($line) => Sequence::of(
                    $line,
                    Element::selfClosing(Name::of('br')),
                )),
        );
    }
}
