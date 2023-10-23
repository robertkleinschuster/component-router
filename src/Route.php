<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;
use Robs\Component\Router\Exception\RouterException;

final class Route
{
    public function __construct(public string $path, public string $file, public RouteType $type, public RouteMethod $method, public ?string $layout = null)
    {
    }

    public function getHandler(): Closure
    {
        if ($this->type === RouteType::PAGE) {
            return require $this->file;
        }
        if ($this->type === RouteType::HANDLER) {
            /** @var Handler $handler */
            $handler = require $this->file;
            switch ($this->method) {
                case RouteMethod::GET:
                    return $handler->get;
                case RouteMethod::POST:
                    return $handler->post;
                case RouteMethod::HEAD:
                    return $handler->head;
                case RouteMethod::PUT:
                    return $handler->put;
                case RouteMethod::DELETE:
                    return $handler->delete;
                case RouteMethod::CONNECT:
                    return $handler->connect;
                case RouteMethod::OPTIONS:
                    return $handler->options;
                case RouteMethod::TRACE:
                    return $handler->trace;
                case RouteMethod::PATCH:
                    return $handler->patch;
            }
        }
        throw new RouterException('Could not locate handler for route.');
    }

    public static function __set_state(array $data): Route
    {
        return new Route($data['path'], $data['file'], $data['type'], $data['method'], $data['layout']);
    }
}