<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;

readonly class Handler
{
    public function __construct(
        public Closure     $handler,
        public RouteMethod $method = RouteMethod::GET,
        public RouteType   $type = RouteType::HANDLER,
        public ?Meta $meta = null,
        public ?object $model = null
    )
    {
    }

    public function __invoke(...$args)
    {
        return ($this->handler)(...$args);
    }
}