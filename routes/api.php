<?php

use App\Controllers\Api\Base;
use App\Middleware\Api\Authentication;
use Slim\Routing\RouteCollectorProxy;

$authentication = new Authentication($container);

$app->group("/api", function(RouteCollectorProxy $api): void {
    $api->any("", Base::class . ":base");
})->add($authentication);
