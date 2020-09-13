<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . "/vendor/autoload.php";

$environment = new Dotenv();

$environment->load(__DIR__ . '/.config');

date_default_timezone_set($_ENV['APP_TIMEZONE']);
mb_internal_encoding($_ENV['APP_CHARSET']);

$container = new Container();

require_once __DIR__ . "/containers/filesystem.php";

$app = AppFactory::createFromContainer($container);

require_once __DIR__ . "/app/routes.php";

$app->run();
