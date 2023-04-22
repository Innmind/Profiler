<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile\Section;

use Innmind\Profiler\Profile\Section;
use Innmind\Filesystem\File\Content;
use Innmind\Html\Element\Script;
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

final class CallGraph implements Section
{
    private Content $json;

    private function __construct(Content $json)
    {
        $this->json = $json;
    }

    public static function of(Content $json): self
    {
        return new self($json);
    }

    public function name(): string
    {
        return 'Call graph';
    }

    public function slug(): string
    {
        return 'call-graph';
    }

    public function render(): Node
    {
        return Element::of(
            'div',
            null,
            Sequence::of(
                Element::of('div', Set::of(Attribute::of('id', 'call-graph'))),
                Script::of(
                    Text::of(<<<D3
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
                    Set::of(Attribute::of('type', 'text/javascript')),
                ),
            ),
        );
    }
}
