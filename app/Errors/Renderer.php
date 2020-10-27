<?php

namespace App\Errors;

use DI\Container;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
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
    // Get message
    $message = "500 internal server error";
    if($throwable instanceof HttpBadRequestException) {
      $message = "400 bad request";
    }
    if($throwable instanceof HttpUnauthorizedException) {
      $message = "401 unauthorized";
    }
    if($throwable instanceof HttpForbiddenException) {
      $message = "403 forbidden";
    }
    if($throwable instanceof HttpNotFoundException) {
      $message = "404 not found";
    }
    if($throwable instanceof HttpMethodNotAllowedException) {
      $message = "405 method not allowed";
    }
    if($throwable instanceof HttpNotImplementedException) {
      $message = "501 not implemented";
    }

    // Get view library
    $view = $this->container->get('view');

    // Return view
    return $view->render('error-template.twig', [
      'message' => $message
    ]);
  }
}
