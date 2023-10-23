<?php

declare(strict_types=1);

namespace Robts\Component\RouterTest;

use Robs\Component\Router\RouteMethod;
use Robs\Component\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private const ROUTES_CACHE = __DIR__ . '/routes-cache.php';
    private const ROUTES = __DIR__ . '/routes';

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

    public function testShouldFindRoutesToPagesInDirectory()
    {
        $router = new Router(self::ROUTES, self::ROUTES_CACHE);
        $routes = $router->getRoutes();

        self::assertCount(5, $routes);

        $indexRoute = $routes[0];
        $subPageRoute = $routes[1];
        $postRoute = $routes[2];
        $subpageGet = $routes[3];
        $subpagePost = $routes[4];

        self::assertEquals(__DIR__ . '/routes/page.php', $indexRoute->file);
        self::assertEquals('/', $indexRoute->path);
        self::assertEquals(__DIR__ . '/routes/sub-page/page.php', $subPageRoute->file);
        self::assertEquals('/sub-page', $subPageRoute->path);
        self::assertEquals(RouteMethod::POST, $postRoute->method);
        self::assertEquals(__DIR__ . '/routes/handler.php', $postRoute->file);

        self::assertEquals('page', $indexRoute->getHandler()());
        self::assertEquals('post', $postRoute->getHandler()());
        self::assertEquals('sub page get', $subpageGet->getHandler()());
        self::assertEquals('sub page post', $subpagePost->getHandler()());
    }
}
