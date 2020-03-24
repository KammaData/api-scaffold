<?php

declare(strict_types=1);

use KammaData\ApiScaffold\Interfaces\DependenciesInterface;

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DemoDependencies implements DependenciesInterface {
    public function __invoke(ContainerBuilder $containerBuilder) {
        $containerBuilder->addDefinitions([
            LoggerInterface::class => function (ContainerInterface $c) {
                $settings = $c->get('settings');

                $loggerSettings = $settings['logger'];
                $logger = new Logger($loggerSettings['name']);

                $processor = new UidProcessor();
                $logger->pushProcessor($processor);

                $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
                $logger->pushHandler($handler);

                return $logger;
            }
        ]);
    }
};

?>
