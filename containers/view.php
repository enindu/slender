<?php

use App\Extensions\FiltersExtension;
use App\Extensions\GlobalsExtension;
use DI\Container;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Template;

$container->set('view', function(Container $container): Environment {
  $loader = new FilesystemLoader(__DIR__ . '/../resources/views/');

  $view = new Environment($loader, [
    'debug'               => $_ENV['VIEW_DEBUG'] === 'true' ? true : false,
    'charset'             => $_ENV['VIEW_CHARSET'],
    'base_template_class' => Template::class,
    'cache'               => __DIR__ . '/../cache',
    'auto_reload'         => $_ENV['VIEW_AUTO_RELOAD'] === 'true' ? true : false,
    'strict_variables'    => $_ENV['VIEW_STRICT_VARIABLES'] === 'true' ? true : false,
    'autoescape'          => 'html',
    'optimizations'       => -1
  ]);

  $filters = new FiltersExtension($container);
  $globals = new GlobalsExtension();

  $view->addExtension($filters);
  $view->addExtension($globals);

  return $view;
});
