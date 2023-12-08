<?php

declare(strict_types=1);

namespace Robs\Component\Router;

class RouteName
{
    public function build(RouteMethod $method, string $path): string
    {
        return $method->name . ' ' . $path;
    }
}