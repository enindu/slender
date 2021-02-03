<?php

namespace System\Slender;

use DI\Container;
use Slim\Psr7\Response;

class Controller
{
  public function __construct(protected Container $container)
  {
    $this->container->get("eloquent");
  }

  protected function view(Response $response, string $template, array $data = []): Response
  {
    $twig = $this->container->get("twig");

    $response->getBody()->write($twig->render($template, $data));
    return $response->withHeader("Content-Type", "text/html");
  }

  protected function validate(array $data, array $rules): null|array
  {
    $validation = $this->container->get("validation");

    $validate = $validation->validate($data, $rules);
    $validationFails = $validate->fails();
    if($validationFails) {
      return $validate->errors()->all();
    }

    return null;
  }
}
