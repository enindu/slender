<?php

use App\Controllers\API\Base;
use App\Middleware\APIAuth;
use Slim\Routing\RouteCollectorProxy;

$app->group("/api", function(RouteCollectorProxy $api) {
  $api->any("", Base::class . ":base");
})->add(new APIAuth($container));
