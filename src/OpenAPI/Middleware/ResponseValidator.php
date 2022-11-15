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

class ResponseValidator implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $request->getAttribute(HandleController::OPEN_API_CONTROLLER_RESPONSE);

        $file = $request->getAttribute(ManifestResolver::OPEN_API_MANIFEST_FILE_PATH);
        $validator = (new \League\OpenAPIValidation\PSR7\ValidatorBuilder)->fromYamlFile($file)->getResponseValidator();
        $match = $request->getAttribute(ManifestResolver::OPEN_API_MANIFEST_DATA_MATCH);
        $operation = new \League\OpenAPIValidation\PSR7\OperationAddress($match->path(), $match->method()) ;
        try {
            $valid = $validator->validate($operation, $response);
        } catch (\Exception $ex) {

        }
        return $response;
    }
}
