<?php

use App\Controllers\Api\Base;
use App\Middleware\Api\Authentication;
use App\Middleware\Api\Method;
use Slim\Routing\RouteCollectorProxy;

$authentication = new Authentication($container);
$putMethod = new Method($container, "PUT");

$app->group("/api", function(RouteCollectorProxy $api) use($putMethod): void {
    $api->any("", Base::class . ":base")->add($putMethod);
})->add($authentication);
