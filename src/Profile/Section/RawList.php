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
use Innmind\Immutable\Sequence;

final class RawList implements Section
{
    /** @var non-empty-string */
    private string $name;
    /** @var non-empty-string */
    private string $slug;
    /** @var Sequence<Content> */
    private Sequence $contents;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $slug
     * @param Sequence<Content> $contents
     */
    private function __construct(string $name, string $slug, Sequence $contents)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->contents = $contents;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $slug
     * @param Sequence<Content> $contents
     */
    public static function of(string $name, string $slug, Sequence $contents): self
    {
        return new self($name, $slug, $contents);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function render(): Node
    {
        return Element::of(
            'div',
            null,
            $this->contents->map(static fn($content) => Element::of(
                'pre',
                null,
                Sequence::of(Element::of(
                    'code',
                    null,
                    Sequence::of(Text::of($content->toString())),
                )),
            )),
        );
    }
}
