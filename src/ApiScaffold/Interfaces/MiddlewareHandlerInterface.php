<?php

declare(strict_types=1);

namespace KammaData\ApiScaffold\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

interface MiddlewareHandlerInterface {
    public function __invoke(Request $request, RequestHandler $handler): Response;
};

?>
