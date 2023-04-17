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
use Innmind\Immutable\Sequence;

final class Environment implements Section
{
    private Content $pairs;

    private function __construct(Content $pairs)
    {
        $this->pairs = $pairs;
    }

    public static function of(Content $pairs): self
    {
        return new self($pairs);
    }

    public function name(): string
    {
        return 'Environment';
    }

    public function slug(): string
    {
        return 'environment';
    }

    public function render(): Node
    {
        return Element::of(
            'code',
            null,
            $this
                ->pairs
                ->lines()
                ->map(static fn($line) => $line->toString())
                ->map(Text::of(...))
                ->flatMap(static fn($line) => Sequence::of(
                    $line,
                    SelfClosingElement::of('br'),
                )),
        );
    }
}
