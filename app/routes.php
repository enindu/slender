<?php

use App\Controllers\User\Base as UserBase;
use Slim\Routing\RouteCollectorProxy;

$app->group("", function(RouteCollectorProxy $user) {
  $user->get("/", UserBase::class . ":base");
});
