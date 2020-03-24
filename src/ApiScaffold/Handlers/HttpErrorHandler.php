<?php

declare(strict_types=1);

namespace KammaData\ApiScaffold\Handlers;

use KammaData\ApiScaffold\Interfaces\ErrorHandlerInterface;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

use Exception;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler implements ErrorHandlerInterface {
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';

    protected function respond(): Response {
        $exception = $this->exception;
        $statusCode = 500;
        $type = self::SERVER_ERROR;
        $description = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();

            if ($exception instanceof HttpNotFoundException) {
                $type = self::RESOURCE_NOT_FOUND;
            }
            else if ($exception instanceof HttpMethodNotAllowedException) {
                $type = self::NOT_ALLOWED;
            }
            else if ($exception instanceof HttpUnauthorizedException) {
                $type = self::UNAUTHENTICATED;
            }
            else if ($exception instanceof HttpForbiddenException) {
                $type = self::UNAUTHENTICATED;
            }
            else if ($exception instanceof HttpBadRequestException) {
                $type = self::BAD_REQUEST;
            }
            else if ($exception instanceof HttpNotImplementedException) {
                $type = self::NOT_IMPLEMENTED;
            }
        }

        if (!($exception instanceof HttpException)
                && ($exception instanceof Exception || $exception instanceof Throwable)
                && $this->displayErrorDetails) {
            $description = $exception->getMessage();
        }

        $error = [
            'status' => [
                'code' => $statusCode,
                'message' => $description
            ]
        ];
        if ($this->displayErrorDetails) {
            $error['status']['type'] = $type;
            $error['status']['file'] = $exception->getFile();
            $error['status']['line'] = $exception->getLine();
        }

        $payload = json_encode($error, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($payload);

        return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json;charset=UTF-8');
    }
};

?>