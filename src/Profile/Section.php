<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Profile;

use Innmind\Xml\Element;

/**
 * @psalm-immutable
 */
interface Section
{
    /**
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * @return non-empty-string
     */
    public function slug(): string;
    public function render(): Element;
}
