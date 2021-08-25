<?php

use DI\Container;
use Slim\Factory\AppFactory;
use System\Slim\ErrorRenderer;

require_once __DIR__ . "/vendor/autoload.php";

require_once __DIR__ . "/settings/app.php";
require_once __DIR__ . "/settings/system.php";

$container = new Container();
require_once __DIR__ . "/libraries/filesystem.php";
require_once __DIR__ . "/libraries/image.php";
require_once __DIR__ . "/libraries/parsedown-extra.php";
require_once __DIR__ . "/libraries/eloquent.php";
require_once __DIR__ . "/libraries/twig.php";
require_once __DIR__ . "/libraries/mailer.php";
require_once __DIR__ . "/libraries/email.php";
require_once __DIR__ . "/libraries/validation.php";

$app = AppFactory::createFromContainer($container);

$routeCachePath = __DIR__ . "/cache/routes/cache.php";
$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile($routeCachePath);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorRenderer = new ErrorRenderer($container);
$errorHandler->registerErrorRenderer("text/html", $errorRenderer);

require_once __DIR__ . "/routes/api.php";
require_once __DIR__ . "/routes/admin.php";
require_once __DIR__ . "/routes/user.php";

$app->run();
