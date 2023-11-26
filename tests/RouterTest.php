<?php

declare(strict_types=1);

namespace Robts\Component\RouterTest;

use Robs\Component\Router\Exception\RouterException;
use Robs\Component\Router\RouteMethod;
use Robs\Component\Router\Router;
use PHPUnit\Framework\TestCase;

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

        self::assertEquals('/', $router->getRoute(RouteMethod::GET, '/')->path);
        self::assertEquals(__DIR__ . '/routes/page.php', $router->getRoute(RouteMethod::GET, '/')->file);
        self::assertEquals(__DIR__ . '/routes/sub-page/page.php', $router->getRoute(RouteMethod::GET, '/sub-page')->file);
        self::assertEquals('/sub-page', $router->getRoute(RouteMethod::GET, '/sub-page')->path);
        self::assertEquals(__DIR__ . '/routes/handler.php', $router->getRoute(RouteMethod::POST, '/')->file);
        self::assertEquals(RouteMethod::POST, $router->getRoute(RouteMethod::POST, '/')->method);
        self::assertEquals('page', $router->getRoute(RouteMethod::GET, '/')->createHandler()());
        self::assertEquals('post', $router->getRoute(RouteMethod::POST, '/')->createHandler()());
        self::assertEquals('sub page', $router->getRoute(RouteMethod::GET, '/sub-page')->createHandler()());
        self::assertEquals('sub page post', $router->getRoute(RouteMethod::POST, '/sub-page')->createHandler()());
        self::assertEquals('sub page put', $router->getRoute(RouteMethod::PUT, '/sub-page')->createHandler()());
        self::assertEquals(__DIR__ . '/routes/layout.php', $router->getRoute(RouteMethod::GET, '/')->layout);
        self::assertEquals(__DIR__ . '/routes/layout.php', $router->getRoute(RouteMethod::GET, '/sub-page')->layout);
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
