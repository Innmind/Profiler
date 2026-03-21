<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Html\Element\Script;
use Innmind\Xml\{
    Node,
    Element,
    Element\Name,
    Attribute,
};
use Innmind\Immutable\Sequence;

/**
 * @internal
 * @psalm-immutable
 */
final class CallGraph implements Section
{
    private Content $json;

    private function __construct(Content $json)
    {
        $this->json = $json;
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(Content $json): self
    {
        return new self($json);
    }

    #[\Override]
    public function name(): string
    {
        return 'Call graph';
    }

    #[\Override]
    public function slug(): string
    {
        return 'call-graph';
    }

    #[\Override]
    public function render(): Element
    {
        return Element::of(
            Name::of('div'),
            null,
            Sequence::of(
                Element::of(Name::of('div'), Sequence::of(Attribute::of('id', 'call-graph'))),
                Script::of(
                    Node::text(<<<D3
                    var flamegraph = d3.flamegraph();
                    flamegraph
                        .inverted(true)
                        .width(document.querySelector('main').clientWidth)
                        .label(function(d) {
                            return d.data.name + ' (' + (100 * (d.x1 - d.x0)).toFixed(2) + '%, ' + d.data.value + ' ms)'
                        })
                    d3.select("#call-graph")
                        .datum({$this->json->toString()})
                        .call(flamegraph);
                    D3),
                    Sequence::of(Attribute::of('type', 'text/javascript')),
                ),
            ),
        );
    }
}
