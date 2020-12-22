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
   * Get globals function
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
      ],
      'auth' => [
        'admin' => [
          'id'       => isset($_SESSION['auth']['admin']['id']) ? $_SESSION['auth']['admin']['id'] : false,
          'role_id'  => isset($_SESSION['auth']['admin']['role-id']) ? $_SESSION['auth']['admin']['role-id'] : false,
          'username' => isset($_SESSION['auth']['admin']['username']) ? $_SESSION['auth']['admin']['username'] : false
        ],
        'user' => [
          'id'         => isset($_SESSION['auth']['user']['id']) ? $_SESSION['auth']['user']['id'] : false,
          'first_name' => isset($_SESSION['auth']['user']['first-name']) ? $_SESSION['auth']['user']['first-name'] : false,
          'last_name'  => isset($_SESSION['auth']['user']['last-name']) ? $_SESSION['auth']['user']['last-name'] : false,
          'email'      => isset($_SESSION['auth']['user']['email']) ? $_SESSION['auth']['user']['email'] : false,
          'phone'      => isset($_SESSION['auth']['user']['phone']) ? $_SESSION['auth']['user']['phone'] : false
        ]
      ],
    ];
  }
}
