<?php

namespace OpenAPI\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TODO refactor, remove duplicate code with https://github.com/rollun-com/rollun-datastore/blob/master/src/DataStore/src/Middleware/ResourceResolver.php
 *
 * Extracts resource name and row id from URL or from request attributes
 *
 * Used request attributes:
 * - resourceName (data store service name)
 * - primaryKeyValue (primary key value to fetch record for record)
 *
 * Examples:
 *
 * - if URL is http://example.com/api/datastore/RESOURCE-NAME/ROW-ID
 *  $request->getAttribute('resourceName') returns 'RESOURCE-NAME'
 *  $request->getAttribute('primaryKeyValue') returns 'ROW-ID'
 *
 * - if URL is http://example.com/api/datastore/RESOURCE-NAME?eq(a,1)&limit(2,5)
 *  $request->getAttribute('resourceName') returns 'RESOURCE-NAME
 *  $request->getAttribute('primaryKeyValue') returns null
 *
 * Class ResourceResolver
 * @package rollun\datastore\Middleware
 */
class PopulateDTO implements MiddlewareInterface
{
    const OPEN_API_DTO_MODEL = "OPEN_API_DTO_MODEL";
    private $defaultDTO = \OpenAPI\DTO\DefaultDTO::class;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $manifestData = $request->getAttribute(ManifestResolver::OPEN_API_MANIFEST_DATA);
        $manifestDataMatch = $request->getAttribute(ManifestResolver::OPEN_API_MANIFEST_DATA_MATCH);
        $path = "\\" .  $manifestData["info"]["title"];
        $path .= "\\v" . $manifestData["info"]["version"];
        $manifestPath = $manifestData['paths'][$manifestDataMatch->path()][$manifestDataMatch->method()]['operationId'];
        $path .= "\DTO\\" . $manifestDataMatch->method() . $manifestPath . "Request";
        if (class_exists($path)) {
            $dto = new $path();
        } else {
            $dto = new $this->defaultDTO();
        }
        $requestQueryParams = $request->getQueryParams();
        $requestBody = $request->getParsedBody();
        $dto->fill($requestQueryParams);
        $dto->fill($requestBody);
        $request = $request->withAttribute(self::OPEN_API_DTO_MODEL, $dto);
        return $handler->handle($request);
    }
}