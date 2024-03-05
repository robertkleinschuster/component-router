<?php

declare(strict_types=1);

use Mosaic\Renderer;
use Compass\Lazy;
use Compass\Route;

return #[Lazy] function (Renderer $r, $children, Route $route) {
    yield $r->fragment(sprintf('<sub id="%s">', $route));
    yield $children;
    yield $r->fragment('</sub>');
};