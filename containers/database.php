<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

$container->set('database', function(): Manager {
  // Create manager
  $manager = new Manager();

  // Configure manager
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

  // Create container and dispatcher
  $container = new Container();
  $dispatcher = new Dispatcher($container);

  // Configure manager
  $manager->setEventDispatcher($dispatcher);
  $manager->setAsGlobal();
  $manager->bootEloquent();

  // Return manager
  return $manager;
});
