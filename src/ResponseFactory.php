<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ResponseFactory
{
    public function createForPage(ServerRequestInterface $request, $page): ResponseInterface;

    public function createFromString(string $result): ResponseInterface;

    public function createFromArray(array $result): ResponseInterface;

    public function createFromJsonSerializable(JsonSerializable $result): ResponseInterface;
}