<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;

readonly class Handler
{
    public function __construct(
        public RouteMethod $method = RouteMethod::GET,
        public RouteType   $type = RouteType::HANDLER,
        public ?Closure    $handler = null,
        public ?Page       $page = null,
        public ?Layout     $layout = null,
        public ?Meta       $meta = null,
        public ?object     $model = null
    )
    {
    }

    public function __invoke(...$args)
    {
        if ($this->type === RouteType::PAGE) {
            return ($this->layout?->view ?? $this->page->view)(
                children: $this->page->view,
                meta: $this->page->meta ?? $this->layout?->meta,
                model: $this->page->model ?? $this->layout?->model
            );
        } else {
            return ($this->handler)(...$args);
        }
    }
}