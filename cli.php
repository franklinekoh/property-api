<?php
require __DIR__.'/vendor/autoload.php';

use App\Commands\SyncProperties;
use Symfony\Component\Console\Application;
use App\Providers\Database\EloquentServiceProvider;


$settings = require __DIR__ . './src/settings.php';
$app = new \Slim\App($settings);

$container = $app->getContainer();

// Register EloquentServiceProvider to use ORM
$container->register(new EloquentServiceProvider());
$propertySyncCommand = new SyncProperties();

$application = new Application();

$application->add($propertySyncCommand);

$application->run();