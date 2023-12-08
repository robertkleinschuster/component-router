<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Closure;
use Robs\Component\Renderer\Renderable;

readonly abstract class BaseView implements Renderable
{
    public function __construct(public Closure $view, public ?Meta $meta = null, public ?object $model = null)
    {
    }
}