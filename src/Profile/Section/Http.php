<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Xml\{
    Node,
    Node\Text,
    Element\Element,
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
                fn($response) => Sequence::of($this->pre($this->request), $this->pre($response)),
                fn() => Sequence::of($this->pre($this->request)),
            ),
        );
    }

    private function pre(Content $content): Node
    {
        return Element::of(
            'pre',
            null,
            Sequence::of(Element::of(
                'code',
                null,
                Sequence::of(Text::of($content->toString())),
            )),
        );
    }
}
