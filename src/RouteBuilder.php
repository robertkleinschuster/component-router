<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;
use Robs\Component\Router\Exception\RouterException;

class RouteBuilder
{
    private RouteName $routeName;

    public function __construct()
    {
        $this->routeName = new RouteName();
    }

    /**
     * @param $handler
     * @param string $filename
     * @param string $path
     * @param int|null $index
     * @return iterable
     * @throws RouterException
     */
    private function transformHandler($handler, string $filename, string $path, int $index = null): iterable
    {
        if ($handler instanceof Handler) {
            $name = $this->routeName->build($handler->method, $path);
            yield new Route(
                name: $name,
                path: $path,
                file: $filename,
                type: $handler->type,
                method: $handler->method,
                index: $index
            );
        } else if ($handler instanceof Closure) {
            $name = $this->routeName->build(RouteMethod::GET, $path);
            yield new Route(
                name: $name,
                path: $path,
                file: $filename,
                type: RouteType::HANDLER,
                method: RouteMethod::GET,
                index: $index
            );
        } else if (is_iterable($handler)) {
            if ($index !== null) {
                throw new RouterException('Invalid return type for handler.');
            }
            foreach ($handler as $index => $h) {
                yield from $this->transformHandler($h, $filename, $path, $index);
            }
        } else {
            throw new RouterException('Invalid return type for handler.');
        }
    }

    /**
     * @param string $routerDirectory
     * @param string $filename
     * @return iterable<Route>
     * @throws RouterException
     */
    public function buildHandler(string $routerDirectory, string $filename): iterable
    {
        $path = dirname(substr($filename, strlen($routerDirectory)));

        $handler = require $filename;

        yield from $this->transformHandler($handler, $filename, $path);
    }


    /**
     * @param string $routerDirectory
     * @param string $filename
     * @param string|null $layout
     * @return iterable<Route>
     */
    public function buildPage(string $routerDirectory, string $filename, string $layout = null): iterable
    {
        $path = dirname(substr($filename, strlen($routerDirectory)));

        $name = $this->routeName->build(RouteMethod::GET, $path);

        yield new Route(
            name: $name,
            path: $path,
            file: $filename,
            type: RouteType::PAGE,
            method: RouteMethod::GET,
            layout: $layout
        );
    }

}