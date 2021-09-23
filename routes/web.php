<?php

use App\Controllers\Web\User\Base;
use Slim\Routing\RouteCollectorProxy;

$app->group("", function(RouteCollectorProxy $web): void {
    $web->get("/", Base::class . ":base");
});
