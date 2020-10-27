<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

$container->set('database', function(): Manager {
  $manager = new Manager();

  $manager->addConnection([
    'driver'    => $_ENV['database']['driver'],
    'host'      => $_ENV['database']['host'],
    'database'  => $_ENV['database']['database'],
    'username'  => $_ENV['database']['username'],
    'password'  => $_ENV['database']['password'],
    'charset'   => $_ENV['database']['charset'],
    'collation' => $_ENV['database']['collation'],
    'prefix'    => $_ENV['database']['prefix']
  ]);

  $container  = new Container();
  $dispatcher = new Dispatcher($container);

  $manager->setEventDispatcher($dispatcher);
  $manager->setAsGlobal();
  $manager->bootEloquent();

  return $manager;
});
