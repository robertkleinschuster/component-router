<?php

declare(strict_types=1);

namespace Robts\Component\RouterTest;

use Robs\Component\Router\Exception\RouterException;
use Robs\Component\Router\RouteMethod;
use Robs\Component\Router\Router;
use PHPUnit\Framework\TestCase;
use Robs\Component\Router\RouteType;

class RouterTest extends TestCase
{
    private const ROUTES_CACHE = __DIR__ . '/routes-cache.php';
    private const ROUTES = __DIR__ . '/routes';
    private const DUPLICATE_ROUTES = __DIR__ . '/duplicate-routes';

    protected function setUp(): void
    {
        parent::setUp();
        unlink(self::ROUTES_CACHE);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink(self::ROUTES_CACHE);
    }

    public function testShouldBuildRouteCacheFromDirectory()
    {
        self::assertFileDoesNotExist(self::ROUTES_CACHE);
        new Router(self::ROUTES, self::ROUTES_CACHE);
        self::assertFileExists(self::ROUTES_CACHE);
    }

    /**
     * @throws RouterException
     */
    public function testShouldFindRoutesToPagesInDirectory()
    {
        $router = new Router(self::ROUTES, self::ROUTES_CACHE);

        self::assertCount(5, $router->getAllRoutes());

        // startpage
        $route = $router->getRoute(RouteMethod::GET, '/');
        self::assertEquals('/', $route->path);
        self::assertEquals(__DIR__ . '/routes/page.php', $route->file);
        self::assertEquals(__DIR__ . '/routes/layout.php', $route->layout);
        self::assertNull($route->index);
        self::assertEquals(RouteMethod::GET, $route->method);
        self::assertEquals(RouteType::PAGE, $route->type);

        // sub-page
        $route = $router->getRoute(RouteMethod::GET, '/sub-page');
        self::assertEquals('/sub-page', $route->path);
        self::assertEquals(__DIR__ . '/routes/sub-page/page.php', $route->file);
        self::assertNull($route->layout);
        self::assertEquals(RouteMethod::GET, $route->method);
        self::assertEquals(RouteType::PAGE, $route->type);

        // handler
        $route = $router->getRoute(RouteMethod::POST, '/');
        self::assertEquals('/', $route->path);
        self::assertEquals(__DIR__ . '/routes/handler.php', $route->file);
        self::assertNull($route->layout);
        self::assertEquals(RouteMethod::POST, $route->method);
        self::assertEquals(RouteType::HANDLER, $route->type);
    }

    public function testBuildRouteNames()
    {
        $router = new Router(self::ROUTES, self::ROUTES_CACHE);
        $route = $router->getRoute(RouteMethod::GET, '/sub-page');

        self::assertEquals('GET /sub-page', $route->name);
    }

    public function testShouldSaveTheIndexForArrayHandlers()
    {
        $router = new Router(self::ROUTES, self::ROUTES_CACHE);

        $route = $router->getRoute(RouteMethod::POST, '/sub-page');
        self::assertEquals(0, $route->index);
        $route = $router->getRoute(RouteMethod::PUT, '/sub-page');
        self::assertEquals(1, $route->index);
    }

    public function testShouldTransferMetaFromPageToHandler()
    {
        $router = new Router(self::ROUTES, self::ROUTES_CACHE);
        $handler = $router->getRoute(RouteMethod::GET, '/sub-page')->createHandler();
        self::assertEquals('sub page title', $handler->meta->title);
        self::assertEquals('sub page description', $handler->meta->description);
    }

    public function testShouldTransferModelFromPageToHandler()
    {
        $router = new Router(self::ROUTES, self::ROUTES_CACHE);
        $handler = $router->getRoute(RouteMethod::GET, '/sub-page')->createHandler();
        self::assertEquals('sub page heading', $handler->model->heading);
    }

    public function testShouldNotAllowDuplicateRouteDefinitions()
    {
        self::expectException(RouterException::class);
        self::expectExceptionMessage('Duplicate route definition: GET /sub-page');
        new Router(self::DUPLICATE_ROUTES, self::ROUTES_CACHE);
    }
}
