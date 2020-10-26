<?php

namespace App\Extensions;

use DI\Container;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Globals extends AbstractExtension implements GlobalsInterface
{
  private $container;

  /**
   * Globals constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  /**
   * Get globals
   * 
   * @return array
   */
  public function getGlobals(): array
  {
    return [
      'unique_id' => uniqid(),
      'app'       => [
        'name'        => $_ENV['APP_NAME'],
        'description' => $_ENV['APP_DESCRIPTION'],
        'keywords'    => $_ENV['APP_KEYWORDS'],
        'author'      => $_ENV['APP_AUTHOR'],
        'url'         => $_ENV['APP_URL']
      ]
    ];
  }
}
