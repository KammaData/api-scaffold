<?php

declare(strict_types=1);

namespace KammaData\ApiScaffold\Interfaces;

use Slim\App;

interface MiddlewareInterface {
    public function __invoke(App $app);
};

?>
