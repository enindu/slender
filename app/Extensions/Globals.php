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
        'name'        => $_ENV['app']['name'],
        'description' => $_ENV['app']['description'],
        'keywords'    => $_ENV['app']['keywords'],
        'author'      => $_ENV['app']['author'],
        'url'         => $_ENV['app']['url']
      ]
    ];
  }
}
