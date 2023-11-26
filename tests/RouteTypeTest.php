<?php

declare(strict_types=1);

namespace Robts\Component\RouterTest;

use Robs\Component\Router\RouteType;
use PHPUnit\Framework\TestCase;

class RouteTypeTest extends TestCase
{
    public function testShouldCompare()
    {
        self::assertTrue(RouteType::HANDLER->is(RouteType::HANDLER));
        self::assertFalse(RouteType::PAGE->is(RouteType::HANDLER));
    }
}
