<?php

use App\Controllers\PagesController;

$app->get('/', PagesController::class . ':home');
