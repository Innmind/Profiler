<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Template;

use Innmind\Profiler\Profile;
use Innmind\Filesystem\File\Content;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;
use Innmind\Html\{
    Node\Document,
    Element\A,
    Element\Img,
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
    Map,
};

final class Index
{
    private Template $profile;

    public function __construct()
    {
        $this->profile = Template::of('/profile/{id}#section-1');
    }

    /**
     * @param Sequence<Profile> $profiles
     */
    public function __invoke(Sequence $profiles): Content
    {
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
                            Element::of('title', null, Sequence::of(Text::of('Profiler'))),
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
                                Sequence::of(A::of(
                                    Url::of('/'),
                                    null,
                                    Sequence::of(Img::of(
                                        Url::of('/logo.svg'),
                                        Set::of(Attribute::of('alt', 'home')),
                                    )),
                                )),
                            ),
                            Element::of(
                                'main',
                                null,
                                Sequence::of(Element::of(
                                    'ul',
                                    null,
                                    $profiles->map(fn($profile) => Element::of(
                                        'li',
                                        Set::of(Attribute::of('class', $profile->status()->name)),
                                        Sequence::of(A::of(
                                            $this->profile->expand(Map::of(['id', $profile->id()->toString()])),
                                            null,
                                            Sequence::of(Text::of($profile->toString())),
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
