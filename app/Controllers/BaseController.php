<?php

namespace App\Controllers;

use DI\Container;
use Slim\Psr7\Response;

class BaseController
{
  protected $filesystem;
  private $container;

  public function __construct(Container $container)
  {
    $this->container = $container;

    $this->filesystem = $container->get('filesystem');
  }

  public function view(Response $response, string $template, array $data = []): Response
  {
    $view = $this->container->get('view');

    $response->withHeader('content-type', 'text/html')->getBody()->write($view->render($template, $data));

    return $response;
  }
}
