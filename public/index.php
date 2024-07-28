<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/debug.php';
if (file_exists(__DIR__ . '/../config/db.php')) {
    require_once __DIR__ . '/../config/db.php';
}
require_once __DIR__ . '/../config/config.php';

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/di.php');
$container = $containerBuilder->build();

require_once __DIR__ . '/../src/routing.php';
