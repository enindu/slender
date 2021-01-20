<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class HtmlMinifier
{
  private $container;

  /**
   * HTML minifier constructor
   * 
   * @param Container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  /**
   * HTML minifier invoker
   * 
   * @param Request                 $request
   * @param RequestHandlerInterface $requestHandler
   * 
   * @return Response
   */
  public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
  {
    // Check header exists
    $response = $requestHandler->handle($request);
    $headerExists = $response->hasHeader('content-type');
    if(!$headerExists) {
      return $response;
    }

    // Check content type HTML exists
    $headers = $response->getHeader('content-type');
    $contentTypeHtmlExists = array_search('text/html', $headers);
    if($contentTypeHtmlExists === false) {
      return $response;
    }

    // Get HTML minify library
    $htmlMinify = $this->container->get('html-minify');

    // Minify content
    $content = (string) $response->getBody();
    $minifiedContent = $htmlMinify->minify($content);

    // Return response
    $response = new Response();
    $response->getBody()->write($minifiedContent);
    return $response;
  }
}
