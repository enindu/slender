<?php

use App\Controllers\User\Base;
use Slim\Routing\RouteCollectorProxy;

$routes = function(RouteCollectorProxy $user): void {
    $user->get("/", Base::class . ":base");
};

$app->group("", $routes);
