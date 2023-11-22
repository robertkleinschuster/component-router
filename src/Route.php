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
            return match ($this->method) {
                RouteMethod::GET => $handler->get,
                RouteMethod::POST => $handler->post,
                RouteMethod::HEAD => $handler->head,
                RouteMethod::PUT => $handler->put,
                RouteMethod::DELETE => $handler->delete,
                RouteMethod::CONNECT => $handler->connect,
                RouteMethod::OPTIONS => $handler->options,
                RouteMethod::TRACE => $handler->trace,
                RouteMethod::PATCH => $handler->patch
            };
        }
        throw new RouterException('Could not locate handler for route.');
    }

    public static function __set_state(array $data): Route
    {
        return new Route($data['path'], $data['file'], $data['type'], $data['method'], $data['layout']);
    }
}