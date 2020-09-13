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

  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  public function __invoke(Throwable $throwable, bool $displayErrorDetails): string
  {
    $view = $this->container->get('view');

    switch($throwable) {
      case $throwable instanceof HttpBadRequestException:
        $data = [
          'message' => '400 bad request'
        ];

        break;

      case $throwable instanceof HttpUnauthorizedException:
        $data = [
          'message' => '401 unauthorized'
        ];

        break;

      case $throwable instanceof HttpForbiddenException:
        $data = [
          'message' => '403 forbidden'
        ];

        break;

      case $throwable instanceof HttpNotFoundException:
        $data = [
          'message' => '404 not found'
        ];

        break;

      case $throwable instanceof HttpMethodNotAllowedException:
        $data = [
          'message' => '405 method not allowed'
        ];

        break;

      case $throwable instanceof HttpNotImplementedException:
        $data = [
          'message' => '501 not implemented'
        ];

        break;

      default:
        $data = [
          'message' => '500 internal server error'
        ];
    }

    return $view->render('error.twig', $data);
  }
}