<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

$container->set('database', function(): Manager {
  $manager = new Manager();

  $manager->addConnection([
    'driver'    => $_ENV['DATABASE_DRIVER'],
    'host'      => $_ENV['DATABASE_HOST'],
    'database'  => $_ENV['DATABASE_DATABASE'],
    'username'  => $_ENV['DATABASE_USERNAME'],
    'password'  => $_ENV['DATABASE_PASSWORD'],
    'charset'   => $_ENV['DATABASE_CHARSET'],
    'collation' => $_ENV['DATABASE_COLLATION'],
    'prefix'    => $_ENV['DATABASE_PREFIX']
  ]);

  $container = new Container();
  $dispatcher = new Dispatcher($container);
  
  $manager->setEventDispatcher($dispatcher);
  $manager->setAsGlobal();
  $manager->bootEloquent();

  return $manager;
});
