<?php

declare(strict_types=1);

use KammaData\ApiScaffold\Interfaces\RoutesInterface;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\App;

class DemoRoutes implements RoutesInterface {
    public function __invoke(App $app) {
        $app->options('/{routes:.+}', function (Request $request, Response $response, array $args): Response {
            return $response;
        });

        $app->get('/', function (Request $request, Response $response, array $args): Response {
            $data = [
                'status' => [
                    'code' => 200,
                    'message' => 'OK'
                ]
            ];
            $payload = json_encode($data, JSON_PRETTY_PRINT);
            $response->getBody()->write($payload);
            return $response->withStatus($data['status']['code'])
                ->withHeader('Content-Type', 'application/json;charset=UTF-8');
        });
    }
};

?>
