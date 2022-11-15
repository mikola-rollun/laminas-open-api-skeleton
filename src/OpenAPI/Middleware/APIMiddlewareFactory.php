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
use Laminas\ServiceManager\Factory\FactoryInterface;

class APIMiddlewareFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|DataStoreApi
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new APIMiddleware($container);
    }
}
