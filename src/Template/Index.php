<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Template;

use Innmind\Profiler\Profile;
use Innmind\Filesystem\File\Content;
use Innmind\UrlTemplate\Template;
use Innmind\Html\{
    Document,
    Element\A,
};
use Innmind\Xml\{
    Document\Type,
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
final class Index
{
    private Template $list;
    private Template $profile;

    public function __construct(Template $list, Template $profile)
    {
        $this->list = $list;
        $this->profile = $profile;
    }

    /**
     * @param Sequence<Profile> $profiles
     */
    public function __invoke(Sequence $profiles): Content
    {
        $document = Document::of(
            Type::of('html'),
            Sequence::of(Element::of(
                Name::of('html'),
                null,
                Sequence::of(
                    Element::of(
                        Name::of('head'),
                        null,
                        Sequence::of(
                            Element::of(Name::of('title'), null, Sequence::of(Node::text('Profiler'))),
                            Element::selfClosing(Name::of('meta'), Sequence::of(Attribute::of('charset', 'UTF-8'))),
                            Element::of(Name::of('style'), null, Sequence::of(Node::text(self::css()))),
                        ),
                    ),
                    Element::of(
                        Name::of('body'),
                        null,
                        Sequence::of(
                            Element::of(
                                Name::of('header'),
                                null,
                                Sequence::of(A::of(
                                    $this->list->expansion()->expand(),
                                    null,
                                    Sequence::of(Element::selfClosing(
                                        Name::of('img'),
                                        Sequence::of(
                                            Attribute::of('alt', 'home'),
                                            Attribute::of('src', 'data:image/svg+xml;base64,PHN2ZyBoZWlnaHQ9IjUwMCIgd2lkdGg9IjUwMCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iMjUwIiBmaWxsPSJyZ2IoNDgsNDgsNDgpIiAvPgogIDxkZWZzPgogICAgPGcgaWQ9ImxlYWYiIGZpbHRlcj0idXJsKCNzaGFkb3cpIj4KICAgICAgPHBhdGggZD0ibSAyNTAgMjAgcSAtNjAgMTI1IDAgMTUwIiBmaWxsPSJ3aGl0ZSIgLz4KICAgICAgPHBhdGggZD0ibSAyNTAgMjAgcSA2MCAxMjUgMCAxNTAiIGZpbGw9IndoaXRlIiAvPgogICAgICA8cG9seWdvbiBwb2ludHM9IjI1MCw4MCAyNTEsMTcwIDI0OSwxNzAiIGZpbGw9InJnYig0OCw0OCw0OCkiIC8+CiAgICA8L2c+CiAgICA8ZmlsdGVyIGlkPSJzaGFkb3ciIHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEyMCUiPgogICAgICA8ZmVPZmZzZXQgcmVzdWx0PSJvZmZPdXQiIGluPSJTb3VyY2VBbHBoYSIgZHg9IjAiIGR5PSI1IiAvPgogICAgICA8ZmVHYXVzc2lhbkJsdXIgcmVzdWx0PSJibHVyT3V0IiBpbj0ib2ZmT3V0IiBzdGREZXZpYXRpb249IjEwMCIgLz4KICAgICAgPGZlQmxlbmQgaW49IlNvdXJjZUdyYXBoaWMiIGluMj0ib2ZmT3V0IiBtb2RlPSJub3JtYWwiIC8+CiAgICA8L2ZpbHRlcj4KICA8L2RlZnM+CiAgPHVzZSB4bGluazpocmVmPSIjbGVhZiIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSg0NSAyNTAgMjUwKSIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSg5MCAyNTAgMjUwKSIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSgxMzUgMjUwIDI1MCkiIC8+CiAgPHVzZSB4bGluazpocmVmPSIjbGVhZiIgdHJhbnNmb3JtPSJyb3RhdGUoMTgwIDI1MCAyNTApIiAvPgogIDx1c2UgeGxpbms6aHJlZj0iI2xlYWYiIHRyYW5zZm9ybT0icm90YXRlKDIyNSAyNTAgMjUwKSIgLz4KICA8dXNlIHhsaW5rOmhyZWY9IiNsZWFmIiB0cmFuc2Zvcm09InJvdGF0ZSgyNzAgMjUwIDI1MCkiIC8+CiAgPHVzZSB4bGluazpocmVmPSIjbGVhZiIgdHJhbnNmb3JtPSJyb3RhdGUoMzE1IDI1MCAyNTApIiAvPgogIDxjaXJjbGUgY3g9IjI1MCIgY3k9IjI1MCIgcj0iNjAiIHN0cm9rZT0icmdiKDUyLDE0MCwyNTUpIiBzdHJva2Utd2lkdGg9IjEwIiBmaWxsPSJub25lIiAvPgo8L3N2Zz4K'),
                                        ),
                                    )),
                                )),
                            ),
                            Element::of(
                                Name::of('main'),
                                null,
                                Sequence::of(Element::of(
                                    Name::of('ul'),
                                    null,
                                    $profiles->map(fn($profile) => Element::of(
                                        Name::of('li'),
                                        Sequence::of(Attribute::of('class', $profile->status()->name)),
                                        Sequence::of(A::of(
                                            $this
                                                ->profile
                                                ->expansion()
                                                ->with('id', $profile->id()->toString())
                                                ->expand(),
                                            null,
                                            Sequence::of(Node::text($profile->toString())),
                                        )),
                                    )),
                                )),
                            ),
                        ),
                    ),
                ),
            )),
        );

        return $document->asContent();
    }

    /**
     * @psalm-pure
     */
    private static function css(): string
    {
        return <<<CSS
        body {
            margin: 0;
        }

        header {
            background-color: rgb(48, 48, 48);
            display: flex;
            height: 60px;
            padding: 0 10px;
        }

        header img {
            height: 50px;
            padding: 5px 0;
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

        main ul li.empty {
            padding: 10px;
        }

        main ul li.succeeded {
            background-color: rgba(104, 255, 101, 0.3);
        }

        main ul li.succeeded:hover {
            background-color: rgba(104, 255, 101, 0.6);
        }

        main ul li.failed {
            background-color: rgba(255, 79, 86, 0.3);
        }

        main ul li.failed:hover {
            background-color: rgba(255, 79, 86, 0.6);
        }

        main ul li.started {
            background-color: rgba(255, 179, 48, 0.3);
        }

        main ul li.started:hover {
            background-color: rgba(255, 179, 48, 0.6);
        }

        main ul li + li {
            border-top: 1px solid rgba(48, 48, 48, 0.2);
        }
        CSS;
    }
}
