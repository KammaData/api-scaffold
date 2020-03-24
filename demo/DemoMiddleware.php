<?php

declare(strict_types=1);

require_once '../DemoCorsHandler.php';

use KammaData\ApiScaffold\Interfaces\MiddlewareInterface;

use Slim\App;

class DemoMiddleware implements MiddlewareInterface {
    public function __invoke(App $app) {
        $app->add(new DemoCorsHandler());
    }
};

?>
