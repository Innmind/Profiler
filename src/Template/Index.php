<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Template;

use Innmind\Profiler\Profile;
use Innmind\Filesystem\File\Content;
use Innmind\UrlTemplate\Template;
use Innmind\UI;
use Innmind\Immutable\{
    Sequence,
    Map,
};

final class Index
{
    private Template $profile;
    private Template $style;
    private Template $logo;

    public function __construct(
        Template $profile,
        Template $style,
        Template $logo,
    ) {
        $this->profile = $profile;
        $this->style = $style;
        $this->logo = $logo;
    }

    /**
     * @param Sequence<Profile> $profiles
     */
    public function __invoke(Sequence $profiles): Content
    {
        return UI\Window::of(
            'Profiler',
            UI\Stack::vertical(
                UI\Toolbar::of(UI\Text::of('Profiler'))
                    ->leading(UI\Image::of($this->logo->expand(Map::of()))),
                UI\Listing::of($profiles->map(
                    fn($profile) => UI\NavigationLink::text(
                        $this->profile->expand(Map::of(['id', $profile->id()->toString()])),
                        $profile->toString(),
                    ),
                )),
            ),
        )
            ->stylesheet($this->style->expand(Map::of()))
            ->render();
    }
}
