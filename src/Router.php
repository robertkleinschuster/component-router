<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Robs\Component\Router\Exception\RouterException;
use SplFileInfo;

class Router
{
    private const PAGE_FILENAME = 'page.php';
    private const HANDLER_FILENAME = 'handler.php';
    private const LAYOUT_FILENAME = 'layout.php';

    private RouteName $routeName;

    /**
     * @var Route[]
     */
    private array $routes;

    public function __construct(private readonly string $directory, private readonly string $cache)
    {
        $this->routeName = new RouteName();
        if (!file_exists($this->cache)) {
            $this->build();
        } else {
            $this->routes = require $this->cache;
        }
    }

    public function getRoute(RouteMethod $method, string $path): ?Route
    {
        return $this->routes[$this->routeName->build($method, $path)] ?? null;
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

    /**
     * @return iterable<Route>
     * @throws RouterException
     */
    private function scanDirectory(): iterable
    {
        $builder = new RouteBuilder();

        $directory = new RecursiveDirectoryIterator($this->directory, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory);

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->getFilename() === self::PAGE_FILENAME) {
                $layout = $item->getPath() . DIRECTORY_SEPARATOR . self::LAYOUT_FILENAME;
                if (!file_exists($layout)) {
                    $layout = null;
                }
                yield from $builder->buildPage($this->directory, $item->getRealPath(), $layout);
            }
            if ($item->getFilename() === self::HANDLER_FILENAME) {
                yield from $builder->buildHandler($this->directory, $item->getRealPath());
            }
        }
    }

    private function build(): void
    {
        foreach ($this->scanDirectory() as $route) {
            $this->addRoute($route);
        }

        $cache = var_export($this->routes, true);
        file_put_contents($this->cache, '<?php return ' . $cache . ';');
    }
}