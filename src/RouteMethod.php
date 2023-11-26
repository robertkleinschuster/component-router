<?php

declare(strict_types=1);

namespace Robs\Component\Router;

enum RouteMethod
{
    case GET;
    case HEAD;
    case POST;
    case PUT;
    case DELETE;
    case CONNECT;
    case OPTIONS;
    case TRACE;
    case PATCH;

    public function is(RouteMethod $method): bool
    {
        return $this === $method;
    }
}
