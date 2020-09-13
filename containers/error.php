<?php

use App\Errors\Renderer;
use DI\Container;

$container->set('renderer', function(Container $container): Renderer {
  return new Renderer($container);
});
