<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Template;

use Innmind\Profiler\Profile as Data;
use Innmind\Filesystem\File\Content;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;
use Innmind\Html\{
    Node\Document,
    Element\A,
};
use Innmind\Xml\{
    Node\Document\Type,
    Node\Text,
    Element\Element,
    Element\SelfClosingElement,
    Attribute,
};
use Innmind\Immutable\{
    Sequence,
    Set,
    Maybe,
    Map,
};

final class Profile
{
    private Template $template;

    public function __construct()
    {
        $this->template = Template::of('/profile/{id}/{section}');
    }

    /**
     * @param Maybe<string> $active
     */
    public function __invoke(Data $profile, Maybe $active): Content
    {
        $name = Element::of(
            'code',
            Set::of(Attribute::of('class', 'name '.$profile->status()->name)),
            Sequence::of(Text::of($profile->toString())),
        );

        $document = Document::of(
            Type::of('html'),
            Sequence::of(Element::of(
                'html',
                null,
                Sequence::of(
                    Element::of(
                        'head',
                        null,
                        Sequence::of(
                            Element::of('title', null, Sequence::of(Text::of('Profile '.$profile->toString()))),
                            SelfClosingElement::of('meta', Set::of(Attribute::of('charset', 'UTF-8'))),
                            Element::of('style', null, Sequence::of(Text::of(self::css()))),
                        ),
                    ),
                    Element::of(
                        'body',
                        null,
                        Sequence::of(
                            Element::of(
                                'header',
                                null,
                                Sequence::of(
                                    A::of(
                                        Url::of('/'),
                                        null,
                                        Sequence::of(SelfClosingElement::of('img', Set::of(
                                            Attribute::of('alt', 'home'),
                                            Attribute::of('src', 'data:image/svg+xml;base64,PHN2ZyBoZWlnaHQ9IjUwMCIgd2lkdGg9IjUwMCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iMjUwIiBmaWxsPSJyZ2IoNDgsNDgsNDgpIiAvPgogIDxkZWZzPgogICAgPGcgaWQ9ImxlYWYiIGZpbHRlcj0idXJsKCNzaGFkb3cpIj4KICAgICAgPHBhdGggZD0ibSAyNTAgMjAgcSAtNjAgMTI1IDAgMTUwIiBmaWxsPSJ3aGl0ZSIgLz4KICAgICAgPHBhdGggZD0ibSAyNTAgMjAgcSA2MCAxMjUgMCAxNTAiIGZpbGw9IndoaXRlIiAvPgogICAgICA8cG9seWdvbiBwb2ludHM9IjI1MCw4MCAyNTEsMTcwIDI0OSwxNzAiIGZpbGw9InJnYig0OCw0OCw0OCkiIC8+CiAgICA8L2c+CiAgICA8ZmlsdGVyIGlkPSJzaGFkb3ciIHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEyMCUiPgogICAgICA8ZmVPZmZzZXQgcmVzdWx0PSJvZmZPdXQiIGluPSJTb3VyY2VBbHBoYSIgZHg9IjAiIGR5PSI1IiAvPgogICAgICA8ZmVHYXVzc2lhbkJsdXIgcmVzdWx0PSJibHVyT3V0IiBpbj0ib2ZmT3V0IiBzdGREZXZpYXRpb249IjEwMCIgLz4KICAgICAgPGZlQmxlbmQgaW49IlNvdXJjZUdyYXBoaWMiIGluMj0ib2ZmT3V0IiBtb2RlPSJub3JtYWwiIC8+CiAgICA8L2ZpbHRlcj4KICA8L2RlZnM+CiAgPHVzZSB4bGluazpocmVmPSIjbGVhZiIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSg0NSAyNTAgMjUwKSIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSg5MCAyNTAgMjUwKSIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSgxMzUgMjUwIDI1MCkiIC8+CiAgPHVzZSB4bGluazpocmVmPSIjbGVhZiIgdHJhbnNmb3JtPSJyb3RhdGUoMTgwIDI1MCAyNTApIiAvPgogIDx1c2UgeGxpbms6aHJlZj0iI2xlYWYiIHRyYW5zZm9ybT0icm90YXRlKDIyNSAyNTAgMjUwKSIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSgyNzAgMjUwIDI1MCkiIC8+CiAgPHVzZSB4bGluazpocmVmPSIjbGVhZiIgdHJhbnNmb3JtPSJyb3RhdGUoMzE1IDI1MCAyNTApIiAvPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iNjAiIHN0cm9rZT0icmdiKDUyLDE0MCwyNTUpIiBzdHJva2Utd2lkdGg9IjEwIiBmaWxsPSJub25lIiAvPgo8L3N2Zz4K'),
                                        ))),
                                    ),
                                    Element::of(
                                        'ul',
                                        null,
                                        $profile->sections()->map(fn($section) => Element::of(
                                            'li',
                                            null,
                                            Sequence::of(A::of(
                                                $this->template->expand(Map::of(
                                                    ['id', $profile->id()->toString()],
                                                    ['section', $section->slug()],
                                                )),
                                                $active
                                                    ->filter(static fn($slug) => $section->slug() === $slug)
                                                    ->match(
                                                        static fn() => Set::of(Attribute::of('class', 'active')),
                                                        static fn() => null,
                                                    ),
                                                Sequence::of(Text::of($section->name())),
                                            )),
                                        )),
                                    ),
                                ),
                            ),
                            Element::of('script', Set::of(
                                Attribute::of('type', 'text/javascript'),
                                Attribute::of('src', 'https://d3js.org/d3.v4.min.js'),
                            )),
                            Element::of('script', Set::of(
                                Attribute::of('type', 'text/javascript'),
                                Attribute::of('src', 'https://cdnjs.cloudflare.com/ajax/libs/d3-tip/0.9.1/d3-tip.min.js'),
                            )),
                            Element::of('script', Set::of(
                                Attribute::of('type', 'text/javascript'),
                                Attribute::of('src', 'https://cdn.jsdelivr.net/gh/spiermar/d3-flame-graph@2.0.3/dist/d3-flamegraph.min.js'),
                            )),
                            Element::of(
                                'main',
                                null,
                                $active
                                    ->flatMap(
                                        static fn($slug) => $profile
                                            ->sections()
                                            ->find(static fn($section) => $section->slug() === $slug),
                                    )
                                    ->map(static fn($section) => Element::of(
                                        'section',
                                        Set::of(Attribute::of('id', 'section-'.$section->slug())),
                                        Sequence::of($section->render()),
                                    ))
                                    ->match(
                                        static fn($section) => Sequence::of($name, $section),
                                        static fn() => Sequence::of($name),
                                    ),
                            ),
                        ),
                    ),
                ),
            )),
        );

        return $document->asContent();
    }

    private static function css(): string
    {
        return <<<CSS
        body {
            margin: 0;
        }

        header {
            background-color: rgb(48, 48, 48);
            display: flex;
            flex-wrap: nowrap;
            height: 60px;
            overflow-x: auto;
            padding: 0 10px;
        }

        header > a {
            flex: 0 0 50px;
        }

        header img {
            height: 50px;
            padding: 5px 0;
        }

        header ul {
            display: flex;
            flex-wrap: nowrap;
            list-style: none;
            margin: 0;
            overflow-x: auto;
            padding: 0;
        }

        header ul li {
            display: inline-block;
            flex: 0 0 auto;
        }

        header ul li a:hover, header ul li a.active {
            background-color: rgb(52,140,255);
        }

        header ul li a {
            color: white;
            display: inline-block;
            padding: 21px 10px;
            text-decoration: none;
        }

        main {
            margin: 35px;
        }

        main ul {
            border: solid;
            border-width: 1px;
            border-color: rgba(48, 48, 48, 0.2);
            margin: 0;
            padding: 0;list-style: none;
            border-radius: 4px;
        }

        main ul li a {
            color: rgb(52,140,255);
            display: block;
            padding: 10px;
            text-decoration: none;
        }

        main ul li.success {
            background-color: rgba(104, 255, 101, 0.3);
        }

        main ul li.success:hover {
            background-color: rgba(104, 255, 101, 0.6);
        }

        main ul li.error {
            background-color: rgba(255, 79, 86, 0.3);
        }

        main ul li.error:hover {
            background-color: rgba(255, 79, 86, 0.6);
        }

        main ul li + li {
            border-top: 1px solid rgba(48, 48, 48, 0.2);
        }

        main > .name {
            border: 1px solid rgba(48, 48, 48, 0.2);
            border-radius: 4px;
            display: block;
            padding: 10px;
        }

        main > .name.succeeded {
            background-color: rgba(104, 255, 101, 0.3);
        }

        main > .name.failed {
            background-color: rgba(255, 79, 86, 0.3);
        }

        main > .name.started {
            background-color: rgba(255, 179, 48, 0.3);
        }

        main section {
            margin-top: 20px;
            max-width: 100%;
        }

        main section code {
            background-color: rgba(48, 48, 48, 0.2);
            border-radius: 4px;
            display: block;
            margin-bottom: 20px;
            padding: 10px;
            word-wrap: break-word;
        }

        main section svg {
            max-width: 100%;
        }

        main section > h3 {
            text-align: center;
        }
        CSS;
    }
}
