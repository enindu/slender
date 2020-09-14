<?php

namespace App\Controllers;

use DI\Container;
use Slim\Psr7\Response;

class Base
{
  protected $filesystem;
  private $container;

  public function __construct(Container $container)
  {
    $this->container = (object) $container;

    $this->filesystem = (object) $container->get('filesystem');

    $container->get('database');
  }

  public function view(Response $response, string $template, array $data = []): Response
  {
    $view = (object) $this->container->get('view');

    $response->withHeader('content-type', 'text/html')->getBody()->write($view->render($template, $data));

    return $response;
  }
}
