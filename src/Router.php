<?php

declare(strict_types=1);

namespace Robs\Component\Router;

final class Router
{
    private const PAGE_FILENAME = 'page.php';
    private const HANDLER_FILENAME = 'handler.php';

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

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    private function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    private function buildRoutes(string $filename, string $suffix): void
    {
        $directory = rtrim($this->directory, '/');

        $pageRelative = substr($filename, strlen($directory));

        $path = substr($pageRelative, 0, strlen($pageRelative) - strlen($suffix));
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        $handler = require $filename;
        if ($handler instanceof Handler) {
            if ($handler->get) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::GET));
            }
            if ($handler->head) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::HEAD));
            }
            if ($handler->post) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::POST));
            }
            if ($handler->put) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::PUT));
            }
            if ($handler->delete) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::DELETE));
            }
            if ($handler->connect) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::CONNECT));
            }
            if ($handler->options) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::OPTIONS));
            }
            if ($handler->trace) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::TRACE));
            }
            if ($handler->patch) {
                $this->addRoute(new Route($path, $filename, RouteType::HANDLER, RouteMethod::PATCH));
            }
        } else {
            $this->addRoute(new Route($path, $filename, RouteType::PAGE, RouteMethod::GET));
        }
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