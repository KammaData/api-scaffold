<?php

declare(strict_types=1);

namespace KammaData\ApiScaffold;

use KammaData\ApiScaffold\Handlers\HttpErrorHandler;
use KammaData\ApiScaffold\Handlers\ShutdownHandler;
use KammaData\ApiScaffold\Interfaces\DependenciesInterface;
use KammaData\ApiScaffold\Interfaces\MiddlewareInterface;
use KammaData\ApiScaffold\Interfaces\RoutesInterface;
use KammaData\ApiScaffold\Interfaces\SettingsInterface;
use KammaData\ApiScaffold\Interfaces\ErrorHandlerInterface;
use KammaData\ApiScaffold\Interfaces\ShutdownHandlerInterface;

use DI\ContainerBuilder;

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter as SlimResponseEmitter;

class Api {
    protected $settings;
    protected $dependencies;
    protected $middleware;
    protected $routes;
    protected $errorHandler;
    protected $shutdownHandler;
    protected $responseEmitter;

    /**
     * Class constructor.
     * @param SettingsInterface         $settings        [description]
     * @param DependenciesInterface     $dependencies    [description]
     * @param MiddlewareInterface       $middleware      [description]
     * @param RoutesInterface           $routes          [description]
     * @param ErrorHandlerInterface     $errorHandler    [description]
     * @param ShutdownHandlerInterface  $shutdownHandler [description]
     */
    public function __construct(SettingsInterface $settings, DependenciesInterface $dependencies, MiddlewareInterface $middleware, RoutesInterface $routes, ErrorHandlerInterface $errorHandler=null, ShutdownHandlerInterface $shutdownHandler=null) {
        $this->settings = $settings;
        $this->dependencies = $dependencies;
        $this->middleware = $middleware;
        $this->routes = $routes;
        $this->errorHandler = $errorHandler;
        $this->shutdownHandler = $shutdownHandler;
    }

    /**
     * Runs and handles the API ...
     */
    public function run(): void {
        $containerBuilder = new ContainerBuilder();

        // Add settings ...
        ($this->settings)($containerBuilder);
        // Add dependencies ...
        ($this->dependencies)($containerBuilder);

        // Build the PHP-DI container ...
        $container = $containerBuilder->build();

        // Create the Slim app ...
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $callableResolver = $app->getCallableResolver();

        $displayErrorDetails = $container->get('settings')['displayErrorDetails'];

        // Create the request object from the server's globals
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $request = $serverRequestCreator->createServerRequestFromGlobals();

        // Create error handler ...
        $responseFactory = $app->getResponseFactory();
        if ($this->errorHandler === null) {
            $this->errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        }

        // Create shutdown handler ...
        if ($this->shutdownHandler === null) {
            $this->shutdownHandler = new ShutdownHandler($request, $this->errorHandler, $displayErrorDetails);
        }
        register_shutdown_function($this->shutdownHandler);

        $app->addBodyParsingMiddleware();

        // Register middleware ...
        ($this->middleware)($app);

        // Add routing middleware ...
        // This needs to be added before any erorr handlers are defined, otherwise
        // any exceptions in the routing middleware will net be passed to the error
        // handler
        $app->addRoutingMiddleware();

        // Register routes ...
        ($this->routes)($app);

        // Add error middleware ...
        $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
        $errorMiddleware->setDefaultErrorHandler($this->errorHandler);

        // Finally run the Slim app and emit the response ...
        $response = $app->handle($request);
        if ($this->responseEmitter === null) {
            $this->responseEmitter = new SlimResponseEmitter();
        }
        $this->responseEmitter->emit($response);
    }
};
