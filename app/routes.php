<?php

use App\Controllers\Base;

$app->get('/', Base::class . ':home');
