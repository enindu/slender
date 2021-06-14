<?php

use App\Controllers\User\Base;
use Slim\Routing\RouteCollectorProxy;

$app->group("", function(RouteCollectorProxy $user) {
  $user->get("/", Base::class . ":base");
});
