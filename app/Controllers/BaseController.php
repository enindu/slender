<?php

namespace App\Controllers;

use DI\Container;

class BaseController
{
  private $container;

  public function __construct(Container $container)
  {
    $this->container = $container;
  }
}
