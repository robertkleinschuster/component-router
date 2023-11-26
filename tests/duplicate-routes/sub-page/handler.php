<?php

declare(strict_types=1);

use Robs\Component\Router\Handler;
use Robs\Component\Router\RouteMethod;

return [
    new Handler(
        handler: fn() => 'sub page duplicate',
        method: RouteMethod::GET,
    ),
    new Handler(
        handler: fn() => 'sub page put',
        method: RouteMethod::PUT
    )
];
