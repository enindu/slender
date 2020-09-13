<?php

namespace App\Controllers;

use DI\Container;

class BaseController
{
  protected $filesystem;
  private $container;

  public function __construct(Container $container)
  {
    $this->container = $container;

    $this->filesystem = $container->get('filesystem');
  }
}
