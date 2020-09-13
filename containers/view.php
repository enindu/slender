<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Template;

$container->set('view', function(): Environment {
  $loader = new FilesystemLoader(__DIR__ . '/../resources/views/');

  return new Environment($loader, [
    'debug'               => $_ENV['VIEW_DEBUG'] === 'true' ? true : false,
    'charset'             => $_ENV['VIEW_CHARSET'],
    'base_template_class' => Template::class,
    'cache'               => __DIR__ . '/../cache',
    'auto_reload'         => $_ENV['VIEW_AUTO_RELOAD'] === 'true' ? true : false,
    'strict_variables'    => $_ENV['VIEW_STRICT_VARIABLES'] === 'true' ? true : false,
    'autoescape'          => 'html',
    'optimizations'       => -1
  ]);
});
