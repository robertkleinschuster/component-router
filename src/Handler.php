<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;

class Handler
{
    public function __construct(
        public ?Closure $get = null,
        public ?Closure $head = null,
        public ?Closure $post = null,
        public ?Closure $put = null,
        public ?Closure $delete = null,
        public ?Closure $connect = null,
        public ?Closure $options = null,
        public ?Closure $trace = null,
        public ?Closure $patch = null,
    )
    {
    }
}