<?php

use App\Extensions\Filters;
use App\Extensions\Globals;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$container->set('view', function() use ($container): Environment {
  // Create filesystem loader
  $filesystemLoader = new FilesystemLoader([
    __DIR__ . '/../resources/views/',
    __DIR__ . '/../resources/templates/'
  ]);

  // Create environment
  $environment = new Environment($filesystemLoader, [
    'debug'               => $_ENV['view']['debug'],
    'charset'             => $_ENV['view']['charset'],
    'base_template_class' => $_ENV['view']['base-template-class'],
    'cache'               => $_ENV['view']['cache'],
    'auto_reload'         => $_ENV['view']['auto-reload'],
    'strict_variables'    => $_ENV['view']['strict-variables'],
    'autoescape'          => $_ENV['view']['auto-escape'],
    'optimizations'       => $_ENV['view']['optimizations']
  ]);

  // Configure environment
  $environment->addExtension(new Filters($container));
  $environment->addExtension(new Globals($container));
  
  // Return environment
  return $environment;
});
