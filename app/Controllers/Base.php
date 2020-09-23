<?php

namespace App\Controllers;

use DI\Container;
use Slim\Psr7\Response;

class Base
{
  protected $filesystem;
  protected $clock;
  protected $image;
  private $container;

  public function __construct(Container $container)
  {
    $this->container = $container;

    $this->filesystem = $container->get('filesystem');
    $this->clock = $container->get('clock');
    $this->image = $container->get('image');

    $container->get('database');
  }

  protected function view(Response $response, string $template, array $data = []): Response
  {
    $view = $this->container->get('view');

    $response->withHeader('content-type', 'text/html')->getBody()->write($view->render($template, $data));

    return $response;
  }

  protected function email(string $template, array $data)
  {
    $message = $this->container->get('message');
    $view = $this->container->get('view');
    $mailer = $this->container->get('mailer');

    $message->setSubject($data['subject']);
    $message->setFrom($data['from']);
    $message->setTo($data['to']);
    $message->setBody($view->render($template, $data['body']), 'text/html');

    $recpients = $mailer->send($message);

    if($recpients === 0) {
      return $recpients;
    }

    return null;
  }

  protected function validate(array $data, array $rules)
  {
    $validator = $this->container->get('validator');

    $validation = $validator->validate($data, $rules);

    $validationFails = $validation->fails();

    if($validationFails) {
      return $validation->errors()->all();
    }

    return null;
  }
}
