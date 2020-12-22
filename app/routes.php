<?php

use App\Controllers\User\Base;

$app->get('/', Base::class . ':home');
