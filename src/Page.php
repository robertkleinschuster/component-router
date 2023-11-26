<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;

readonly class Page
{
    public function __construct(public Closure $view, public ?Meta $meta = null, public ?object $model = null)
    {
    }
}