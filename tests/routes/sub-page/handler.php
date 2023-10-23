<?php

declare(strict_types=1);

use Robs\Component\Router\Handler;

return new Handler(
    post: fn() => 'sub page post',
    put: fn() => 'sub page put'
);