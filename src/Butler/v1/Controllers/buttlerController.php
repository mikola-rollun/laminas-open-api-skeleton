<?php

namespace Butler\v1\Controllers;

use OpenAPI\DTO\DefaultDTO;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class buttlerController {
    function greetRequester(ServerRequestInterface $request, DefaultDTO $dto): DefaultDTO {
        $result = ["Ba" => 123];
        return new DefaultDTO($result);
    }
}