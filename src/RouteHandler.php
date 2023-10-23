<?php

declare(strict_types=1);

namespace Robs\Component\Router;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Robs\Component\Router\Exception\RouterException;

final readonly class RouteHandler implements RequestHandlerInterface
{
    public function __construct(private Route $route, private ResponseFactory $responseFactory)
    {
    }

    /**
     * @throws RouterException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->route->getHandler();
        if ($this->route->type === RouteType::PAGE) {
            return $this->responseFactory->createForPage($request, $handler);
        }
        if ($this->route->type === RouteType::HANDLER) {
            $result = $handler($request);
            if ($result instanceof ResponseInterface) {
                return $result;
            }
            if (is_string($result)) {
                return $this->responseFactory->createFromString($result);
            }
            if (is_array($result)) {
                return $this->responseFactory->createFromArray($result);
            }
            if ($result instanceof JsonSerializable) {
                return $this->responseFactory->createFromJsonSerializable($result);
            }
        }
        throw new RouterException('Could not create response for route.');
    }
}