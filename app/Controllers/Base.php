<?php

namespace App\Controllers;

use DI\Container;
use Intervention\Image\ImageManager;
use Slim\Psr7\Response;

class Base
{
  protected $filesystem;
  protected $image;
  private $container;

  public function __construct(Container $container)
  {
    $this->container = (object) $container;

    $this->filesystem = (object) $container->get('filesystem');
    $this->image = (object) $container->get('image');

    $container->get('database');
  }

  protected function view(Response $response, string $template, array $data = []): Response
  {
    $view = (object) $this->container->get('view');

    $response->withHeader('content-type', 'text/html')->getBody()->write($view->render($template, $data));

    return $response;
  }

  protected function email(string $template, array $data)
  {
    $message = (object) $this->container->get('message');
    $view = (object) $this->container->get('view');
    $mailer = (object) $this->container->get('mailer');

    $message->setSubject($data['subject']);
    $message->setFrom($data['from']);
    $message->setTo($data['to']);
    $message->setBody($view->render($template, $data['body']), 'text/html');

    $recpients = (int) $mailer->send($message);

    if($recpients === 0) {
      return $recpients;
    }

    return null;
  }

  protected function validate(array $data, array $rules)
  {
    $validator = $this->container->get('validator');

    $validation = $validator->validate($data, $rules);

    if($validation->fails()) {
      return $validation->errors()->all();
    }

    return null;
  }
}
