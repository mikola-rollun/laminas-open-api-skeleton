<?php

/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace OpenAPI\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Container\ContainerInterface;

class APIMiddleware implements MiddlewareInterface
{
    protected $middlewarePipe;
    protected $container;

    public function __construct(
        ContainerInterface $container,
    ) {
        $this->container = $container;
        $this->middlewarePipe = new MiddlewarePipe();

        // Это можно вынести в фабрику
        $middlewares = array_merge([
            new ManifestResolver(),
            new PopulateDTO(),
            new HandleController($this->container),
            // new ResponseGenerator(), //TODO
            new ResponseValidator(),
        ]);

        foreach ($middlewares as $middleware) {
            $this->middlewarePipe->pipe($middleware);
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $this->middlewarePipe->process($request, $handler);
            return $response;
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }
    }
}
