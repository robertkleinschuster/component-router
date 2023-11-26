<?php

declare(strict_types=1);

namespace Robts\Component\RouterTest;

use Robs\Component\Router\RouteMethod;
use PHPUnit\Framework\TestCase;

class RouteMethodTest extends TestCase
{
    public function testShouldCompareMethods()
    {
        self::assertTrue(RouteMethod::GET->is(RouteMethod::GET));
        self::assertFalse(RouteMethod::POST->is(RouteMethod::GET));
    }
}
