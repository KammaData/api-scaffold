<?php

require_once '../../vendor/autoload.php';

require_once '../DemoSettings.php';
require_once '../DemoDependencies.php';
require_once '../DemoMiddleware.php';
require_once '../DemoRoutes.php';

use KammaData\ApiScaffold\Api;

$settings = new DemoSettings();
$dependencies = new DemoDependencies();
$middleware = new DemoMiddleware();
$routes = new DemoRoutes();

$api = new Api($settings, $dependencies, $middleware, $routes);
$api->run();

?>
