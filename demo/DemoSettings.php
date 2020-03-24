<?php

declare(strict_types=1);

use KammaData\ApiScaffold\Interfaces\SettingsInterface;

use DI\ContainerBuilder;
use Monolog\Logger;

class DemoSettings implements SettingsInterface {
    public function __invoke(ContainerBuilder $containerBuilder) {
        $containerBuilder->addDefinitions([
            'settings' => [
                'displayErrorDetails' => true, // Should be set to false in production
                'logger' => [
                    'name' => 'api-scaffold-demo',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ]
            ]
        ]);
    }
};

?>
