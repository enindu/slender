<?php

use App\Errors\Renderer;
use DI\Container;
use Slim\Factory\AppFactory;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/bootstrap/bootstrap.php";

date_default_timezone_set($_ENV['app']['timezone']);
mb_internal_encoding($_ENV['app']['charset']);

$container = new Container();

require_once __DIR__ . "/containers/session-middleware.php";
require_once __DIR__ . "/containers/filesystem.php";
require_once __DIR__ . "/containers/clock.php";
require_once __DIR__ . "/containers/image.php";
require_once __DIR__ . "/containers/database.php";
require_once __DIR__ . "/containers/view.php";
require_once __DIR__ . "/containers/mailer.php";
require_once __DIR__ . "/containers/message.php";
require_once __DIR__ . "/containers/validator.php";

$app = AppFactory::createFromContainer($container);

require_once __DIR__ . "/app/middleware.php";

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler    = $errorMiddleware->getDefaultErrorHandler();

$errorHandler->registerErrorRenderer('text/html', new Renderer($container));

require_once __DIR__ . "/app/routes.php";

$app->run();
