<?php

namespace App\Errors;

use DI\Container;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class Renderer implements ErrorRendererInterface
{
  private $container;

  /**
   * Renderer constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  /**
   * Renderer invoker
   * 
   * @param Throwable $throwable
   * @param bool      $displayErrorDetails
   * 
   * @return string
   */
  public function __invoke(Throwable $throwable, bool $displayErrorDetails): string
  {
    // Get view library
    $view = $this->container->get('view');

    // Return view
    return $view->render('error-template.twig', [
      'code'    => $throwable->getCode(),
      'message' => $throwable->getCode(),
      'file'    => $throwable->getFile(),
      'line'    => $throwable->getLine(),
      'traces'  => $throwable->getTrace()
    ]);
  }
}
