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
    // Get data
    $data['message'] = "500 internal server error";

    // Check throwable
    if($throwable instanceof HttpBadRequestException) {
      $data['message'] = "400 bad request";
    }

    if($throwable instanceof HttpUnauthorizedException) {
      $data['message'] = "401 unauthorized";
    }

    if($throwable instanceof HttpForbiddenException) {
      $data['message'] = "403 forbidden";
    }

    if($throwable instanceof HttpNotFoundException) {
      $data['message'] = "404 not found";
    }

    if($throwable instanceof HttpMethodNotAllowedException) {
      $data['message'] = "405 method not allowed";
    }

    if($throwable instanceof HttpNotImplementedException) {
      $data['message'] = "501 not implemented";
    }

    // Get view library
    $view = $this->container->get('view');

    // Return view
    return $view->render('error-template.twig', $data);
  }
}
