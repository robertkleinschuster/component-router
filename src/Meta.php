<?php

declare(strict_types=1);

namespace Robs\Component\Router;

readonly class Meta
{
    public function __construct(public string $title, public string $description)
    {
    }
}