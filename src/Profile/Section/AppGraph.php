<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Xml\{
    Node,
    Node\Text,
    Element\Element,
    Attribute,
};
use Innmind\Immutable\{
    Sequence,
    Set,
};

final class AppGraph implements Section
{
    private Content $svg;

    private function __construct(Content $svg)
    {
        $this->svg = $svg;
    }

    public static function of(Content $svg): self
    {
        return new self($svg);
    }

    public function name(): string
    {
        return 'App graph';
    }

    public function slug(): string
    {
        return 'app-graph';
    }

    public function render(): Node
    {
        return Element::of(
            'div',
            null,
            Sequence::of(
                Element::of(
                    'a',
                    Set::of(
                        Attribute::of('href', 'data:image/svg+xml;base64,'.\base64_encode($this->svg->toString())),
                        Attribute::of('download', 'app-graph.svg'),
                    ),
                    Sequence::of(Text::of('Download')),
                ),
                Text::of($this->svg->toString()),
            ),
        );
    }
}
