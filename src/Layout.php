<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Robs\Component\Renderer\Renderer;

readonly class Layout extends BaseView
{
    public function render(Renderer $renderer, $data = null): iterable
    {
        yield ['meta' => $this->meta, 'model' => $this->model, ] => $this->view;
    }
}