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
class ManifestResolver implements MiddlewareInterface
{
    const OPEN_API_MANIFEST_FILE_PATH = 'OPEN_API_MANIFEST_FILE_PATH';
    const OPEN_API_MANIFEST_DATA = 'OPEN_API_MANIFEST_DATA';
    const OPEN_API_MANIFEST_DATA_MATCH = 'OPEN_API_MANIFEST_DATA_MATCH';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $rootDirectory = $this->getRootDir(__DIR__);
        $files = $this->listAllFiles($rootDirectory . '/manifests/');

        foreach($files as $file) {
            $fileContents = file_get_contents($file);
            $parsed = yaml_parse($fileContents);
            $validator = (new \League\OpenAPIValidation\PSR7\ValidatorBuilder)->fromYaml($fileContents)->getServerRequestValidator();
            try {
                $match = $validator->validate($request);
            } catch (\Exception $ex) {
                //do nothing for now
            }
            $request = $request->withAttribute(self::OPEN_API_MANIFEST_FILE_PATH, $file);
            $request = $request->withAttribute(self::OPEN_API_MANIFEST_DATA, $parsed);
            $request = $request->withAttribute(self::OPEN_API_MANIFEST_DATA_MATCH, $match);
            return $handler->handle($request);
        }
        throw new \Exception("OpenAPI: Unable to find mathing route");
    }

    private function getRootDir($dirPath, $allowedDepth = 10) {
        $dirPath = (dirname($dirPath));
        if ($allowedDepth == 0) {
            throw new \Exception("Unable to find root folder");
        }
        if (file_exists($dirPath . '/vendor/autoload.php')) {
            return $dirPath;
        }
        return $this->getRootDir($dirPath, $allowedDepth - 1);
    }

    private function listAllFiles($dir) {
        $array = array_diff(scandir($dir), array('.', '..'));
       
        foreach ($array as &$item) {
          $item = $dir . $item;
        }
        unset($item);
        foreach ($array as $item) {
          if (is_dir($item)) {
           $array = array_merge($array, $this->listAllFiles($item . DIRECTORY_SEPARATOR));
          }
        }
        return $array;
      }
}