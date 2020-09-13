<?php

use App\Controllers\Pages;

$app->get('/', Pages::class . ':home');
