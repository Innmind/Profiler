<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Xml\{
    Node,
    Node\Text,
    Element\Element,
    Element\SelfClosingElement,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
};

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
     * @param Maybe<Content> $response
     */
    public static function of(Content $request, Maybe $response): self
    {
        return new self($request, $response);
    }

    public function name(): string
    {
        return 'Http';
    }

    public function slug(): string
    {
        return 'http';
    }

    public function render(): Node
    {
        return Element::of(
            'div',
            null,
            $this->response->match(
                fn($response) => Sequence::of($this->wrap($this->request), $this->wrap($response)),
                fn() => Sequence::of($this->wrap($this->request)),
            ),
        );
    }

    private function wrap(Content $content): Node
    {
        return Element::of(
            'code',
            null,
            $content
                ->lines()
                ->map(static fn($line) => $line->toString())
                ->map(\htmlspecialchars(...))
                ->map(Text::of(...))
                ->flatMap(static fn($line) => Sequence::of(
                    $line,
                    SelfClosingElement::of('br'),
                )),
        );
    }
}
