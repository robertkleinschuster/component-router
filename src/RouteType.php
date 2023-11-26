<?php

declare(strict_types=1);

namespace Robs\Component\Router;

enum RouteType
{
    case PAGE;
    case HANDLER;

    public function is(RouteType $type): bool
    {
        return $this === $type;
    }
}
