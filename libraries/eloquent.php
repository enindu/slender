<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

$manager = new Manager();
$manager->addConnection([
  "driver"    => $_ENV["database"]["driver"],
  "host"      => $_ENV["database"]["host"],
  "database"  => $_ENV["database"]["database"],
  "username"  => $_ENV["database"]["username"],
  "password"  => $_ENV["database"]["password"],
  "charset"   => $_ENV["database"]["charset"],
  "collation" => $_ENV["database"]["collation"],
  "prefix"    => $_ENV["database"]["prefix"]
]);
$manager->setEventDispatcher(new Dispatcher(new Container()));
$manager->setAsGlobal();
$manager->bootEloquent();
return $manager;
