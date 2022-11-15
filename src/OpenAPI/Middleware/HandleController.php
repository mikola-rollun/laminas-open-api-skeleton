<?php

namespace OpenAPI\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class HandleController implements MiddlewareInterface {
    const OPEN_API_CONTROLLER_RESPONSE = "OPEN_API_CONTROLLER_RESPONSE";
    protected $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $manifestData = $request->getAttribute(ManifestResolver::OPEN_API_MANIFEST_DATA);
        $manifestDataMatch = $request->getAttribute(ManifestResolver::OPEN_API_MANIFEST_DATA_MATCH);
        $path = "\\" .  $manifestData["info"]["title"];
        $path .= "\\v" . $manifestData["info"]["version"];
        $tagName = $manifestData['paths'][$manifestDataMatch->path()][$manifestDataMatch->method()]['tags'][0];
        $path .= "\Controllers\\" . $tagName . "Controller";
        if (!class_exists($path)) {
            throw new \Exception("Unable to find " . $path);
        }
        if ($this->container->has($path)) {
            $controller = $this->container->get($path);
        } else {
            try {
                $controller = new $path();
            } catch (\Exception $ex) {
                throw new \Exception("Unable to create controller " . $path);
            }
        }
        $operationId = $manifestData['paths'][$manifestDataMatch->path()][$manifestDataMatch->method()]['operationId'];

        if (!method_exists($controller, $operationId)) {
            throw new \Exception("Unable to call " . $path . " (" . $operationId . ")");   
        }
        // $reflectionClass = new \ReflectionClass($controller);
        // $returnType = $reflectionClass->getMethod($operationId)->getReturnType();
        // if ($returnType != ResponseInterface::class) {
        //     throw new \Exception("The controller doesn't meet response schema (Psr\Http\Message\ResponseInterface)");
        // }
        // $response = $controller->{$operationId}($request, $request->getAttribute(PopulateDTO::OPEN_API_DTO_MODEL));
        // $request = $request->withAttribute(self::OPEN_API_CONTROLLER_RESPONSE, $response);
        return $handler->handle($request);
    }
}