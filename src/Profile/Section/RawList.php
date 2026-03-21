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
     * @internal
     *
     * @param non-empty-string $name
     * @param non-empty-string $slug
     * @param Sequence<Content> $contents
     */
    public static function of(string $name, string $slug, Sequence $contents): self
    {
        return new self($name, $slug, $contents);
    }

    #[\Override]
    public function name(): string
    {
        return $this->name;
    }

    #[\Override]
    public function slug(): string
    {
        return $this->slug;
    }

    #[\Override]
    public function render(): Element
    {
        return Element::of(
            Name::of('div'),
            null,
            $this->contents->map(static fn($content) => Element::of(
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
            )),
        );
    }
}
