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
use Psr\Container\ContainerInterface;

class ResponseGenerator implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $request->getAttribute(HandleController::OPEN_API_CONTROLLER_RESPONSE);
        $accepts = $request->getHeader("accept");
        if (isset($accept[0])) {
            $accepts = explode(",", $accept[0]);
            foreach ($accepts as $accept) {
                switch ($accept) {
                    case "application/json":
                        
                    case "application/xml":
                    case "application/x-yaml":
                }
            }
        }
    }
}
