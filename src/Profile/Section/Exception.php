<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Xml\{
    Node,
    Element,
    Element\Name,
    Attribute,
};
use Innmind\Immutable\Sequence;

final class Exception implements Section
{
    private Content $svg;

    private function __construct(Content $svg)
    {
        $this->svg = $svg;
    }

    /**
     * @internal
     */
    public static function of(Content $svg): self
    {
        return new self($svg);
    }

    #[\Override]
    public function name(): string
    {
        return 'Exception';
    }

    #[\Override]
    public function slug(): string
    {
        return 'exception';
    }

    #[\Override]
    public function render(): Element
    {
        return Element::of(
            Name::of('div'),
            null,
            Sequence::of(
                Element::of(
                    Name::of('a'),
                    Sequence::of(
                        Attribute::of('href', 'data:image/svg+xml;base64,'.\base64_encode($this->svg->toString())),
                        Attribute::of('download', 'stack-trace.svg'),
                    ),
                    Sequence::of(Node::text('Download')),
                ),
                Node::raw($this->svg->toString()),
            ),
        );
    }
}
