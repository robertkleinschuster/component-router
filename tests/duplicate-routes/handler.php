<?php

declare(strict_types=1);

use Robs\Component\Router\Handler;
use Robs\Component\Router\RouteMethod;

return new Handler(
    handler: fn() => 'post',
    method: RouteMethod::POST,
);