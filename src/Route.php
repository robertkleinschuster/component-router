<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use Robs\Component\Router\Exception\RouterException;

final readonly class Route
{
    public function __construct(
        public string      $name,
        public string      $path,
        public string      $file,
        public RouteType   $type,
        public RouteMethod $method,
        public ?int        $index = null,
        public ?string     $layout = null,
    )
    {
    }

    /**
     * @return Handler
     * @throws RouterException
     */
    public function createHandler(): Handler
    {
        if ($this->type === RouteType::PAGE) {
            $page = require $this->file;
            if ($page instanceof Page) {
                return new Handler(
                    handler: $page->view,
                    method: $this->method,
                    type: $this->type,
                    meta: $page->meta,
                    model: $page->model
                );
            } else {
                return new Handler(
                    handler: $page,
                    method: $this->method,
                    type: $this->type
                );
            }
        }
        if ($this->type === RouteType::HANDLER) {
            /** @var Handler|Handler[] $handler */
            $handler = require $this->file;

            if (null === $this->index) {
                return $handler;
            } else {
                return $handler[$this->index];

            }
        }
        throw new RouterException('Could not locate handler for route.');
    }

    public static function __set_state(array $data): Route
    {
        return new Route(
            name: $data['name'],
            path: $data['path'],
            file: $data['file'],
            type: $data['type'],
            method: $data['method'],
            index: $data['index'],
            layout: $data['layout']
        );
    }
}