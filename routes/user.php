<?php

use App\Controllers\User\Base;
use Slim\Routing\RouteCollectorProxy;

$app->group("", function(RouteCollectorProxy $user): void {
    $user->get("/", Base::class . ":base");
});
