<?php

declare(strict_types=1);

namespace KammaData\ApiScaffold\Interfaces;

use DI\ContainerBuilder;

interface DependenciesInterface {
    public function __invoke(ContainerBuilder $builder);
};

?>
