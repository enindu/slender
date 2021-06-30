<?php

use App\Controllers\Api\Base;
use App\Middleware\Api\Authentication;
use Slim\Routing\RouteCollectorProxy;

$authentication = new Authentication($container);

$routes = function(RouteCollectorProxy $api): void {
    $api->any("", Base::class . ":base");
};

$app->group("/api", $routes)->add($authentication);
