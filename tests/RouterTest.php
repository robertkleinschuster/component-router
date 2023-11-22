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

        self::assertEquals('/', $router->match(RouteMethod::GET, '/')->path);
        self::assertEquals(__DIR__ . '/routes/page.php', $router->match(RouteMethod::GET, '/')->file);
        self::assertEquals(__DIR__ . '/routes/sub-page/page.php', $router->match(RouteMethod::GET, '/sub-page')->file);
        self::assertEquals('/sub-page', $router->match(RouteMethod::GET, '/sub-page')->path);
        self::assertEquals(__DIR__ . '/routes/handler.php', $router->match(RouteMethod::POST, '/')->file);
        self::assertEquals(RouteMethod::POST, $router->match(RouteMethod::POST, '/')->method);
        self::assertEquals('page', $router->match(RouteMethod::GET, '/')->getHandler()());
        self::assertEquals('post', $router->match(RouteMethod::POST, '/')->getHandler()());
        self::assertEquals('sub page', $router->match(RouteMethod::GET, '/sub-page')->getHandler()());
        self::assertEquals('sub page post', $router->match(RouteMethod::POST, '/sub-page')->getHandler()());
        self::assertEquals('sub page put', $router->match(RouteMethod::PUT, '/sub-page')->getHandler()());
        self::assertEquals(__DIR__ . '/routes/layout.php', $router->match(RouteMethod::GET, '/')->layout);
        self::assertEquals(__DIR__ . '/routes/layout.php', $router->match(RouteMethod::GET, '/sub-page')->layout);
    }
}
