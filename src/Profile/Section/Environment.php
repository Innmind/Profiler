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

final class Environment implements Section
{
    private Content $pairs;

    private function __construct(Content $pairs)
    {
        $this->pairs = $pairs;
    }

    /**
     * @internal
     */
    public static function of(Content $pairs): self
    {
        return new self($pairs);
    }

    #[\Override]
    public function name(): string
    {
        return 'Environment';
    }

    #[\Override]
    public function slug(): string
    {
        return 'environment';
    }

    #[\Override]
    public function render(): Element
    {
        return Element::of(
            Name::of('code'),
            null,
            $this
                ->pairs
                ->lines()
                ->map(static fn($line) => $line->toString())
                ->map(Node::text(...))
                ->flatMap(static fn($line) => Sequence::of(
                    $line,
                    Element::selfClosing(Name::of('br')),
                )),
        );
    }
}
