<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;
use Robs\Component\Router\Exception\RouterException;

class Router
{
    private const PAGE_FILENAME = 'page.php';
    private const HANDLER_FILENAME = 'handler.php';
    private const LAYOUT_FILENAME = 'layout.php';

    /**
     * @var Route[]
     */
    private array $routes;

    public function __construct(private readonly string $directory, private readonly string $cache)
    {
        if (!file_exists($this->cache)) {
            $this->build();
        } else {
            $this->routes = require $this->cache;
        }
    }

    public function getRoute(RouteMethod $method, string $path): ?Route
    {
        return $this->routes[$this->buildRouteName($method, $path)] ?? null;
    }

    /**
     * @return Route[]
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }

    private function addRoute(Route $route): void
    {
        if (isset($this->routes[$route->name])) {
            throw new RouterException(sprintf('Duplicate route definition: %s', $route->name));
        }
        $this->routes[$route->name] = $route;
    }

    private function buildRoutes(string $filename, string $suffix): void
    {
        $directory = rtrim($this->directory, '/');

        $pageRelative = substr($filename, strlen($directory));

        $path = substr($pageRelative, 0, strlen($pageRelative) - strlen($suffix));
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        /** @var Closure|Handler|Handler[] $handler */
        $handler = require $filename;
        if ($handler instanceof Handler) {
            $this->addRoute(new Route(
                name: $this->buildRouteName($handler->method, $path),
                path: $path,
                file: $filename,
                type: $handler->type,
                method: $handler->method
            ));
        } elseif (is_array($handler)) {
            foreach ($handler as $index => $h) {
                $this->addRoute(new Route(
                    name: $this->buildRouteName($h->method, $path),
                    path: $path,
                    file: $filename,
                    type: $h->type,
                    method: $h->method,
                    index: $index
                ));
            }
        } else {
            $layout = dirname($filename) . '/' . self::LAYOUT_FILENAME;

            while (!file_exists($layout) && dirname($layout) !== $this->directory) {
                $layout = dirname($layout, 2) . '/' . self::LAYOUT_FILENAME;
            }

            if (!file_exists($layout)) {
                $layout = null;
            }

            $this->addRoute(new Route(
                name: $this->buildRouteName(RouteMethod::GET, $path),
                path: $path,
                file: $filename,
                type: RouteType::PAGE,
                method: RouteMethod::GET,
                layout: $layout
            ));
        }
    }

    private function buildRouteName(RouteMethod $method, string $path): string
    {
        return $method->name . ' ' . $path;
    }

    private function buildFilename(string $filename): void
    {
        $directory = rtrim($this->directory, '/');
        $startPage = $directory . '/' . $filename;
        if (file_exists($startPage)) {
            $this->buildRoutes($startPage, $filename);
        }

        $pattern = $directory . '/**/' . $filename;
        $files = glob($pattern);
        foreach ($files as $file) {
            $this->buildRoutes($file, $filename);
        }
    }


    private function build(): void
    {
        $this->buildFilename(self::PAGE_FILENAME);
        $this->buildFilename(self::HANDLER_FILENAME);

        $cache = var_export($this->routes, true);
        file_put_contents($this->cache, '<?php return ' . $cache . ';');
    }
}