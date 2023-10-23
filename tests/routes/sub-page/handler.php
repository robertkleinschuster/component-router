<?php

declare(strict_types=1);

use Robs\Component\Router\Handler;

return new Handler(
    get: fn() => 'sub page get',
    post: fn() => 'sub page post'
);