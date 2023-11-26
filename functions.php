<?php

declare(strict_types=1);

use Robs\Component\Router\Handler;
use Robs\Component\Router\Meta;
use Robs\Component\Router\Page;
use Robs\Component\Router\RouteMethod;
use Robs\Component\Router\RouteType;

function handler(
    Closure     $handler,
    RouteMethod $method = RouteMethod::GET,
    RouteType   $type = RouteType::HANDLER,
    ?Meta       $meta = null
): Handler
{
    return new Handler($handler, $method, $type, $meta);
}

function handle_post(Closure $handler): Handler
{
    return handler($handler, RouteMethod::POST);
}

function handle_put(Closure $handler): Handler
{
    return handler($handler, RouteMethod::PUT);
}

function handle_patch(Closure $handler): Handler
{
    return handler($handler, RouteMethod::PATCH);
}

function handle_delete(Closure $handler): Handler
{
    return handler($handler, RouteMethod::DELETE);
}

function handle_get(Closure $handler): Handler
{
    return handler($handler);
}

function page(Closure $view, ?Meta $meta = null, ?object $model = null): Page
{
    return new Page($view, $meta, $model);
}

function meta(string $language, string $title, string $description): Meta
{
    return new Meta($language, $title, $description);
}