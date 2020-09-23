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

  /**
   * Base constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;

    $this->filesystem = $container->get('filesystem');
    $this->clock = $container->get('clock');
    $this->image = $container->get('image');

    $container->get('database');
  }

  /**
   * View function
   * 
   * @param Response $response
   * @param string   $template
   * @param array    $data
   * 
   * @return Response
   */
  protected function view(Response $response, string $template, array $data = []): Response
  {
    $view = $this->container->get('view');

    $response->withHeader('content-type', 'text/html')->getBody()->write($view->render($template, $data));

    return $response;
  }

  /**
   * Mail function
   * 
   * @param string $template
   * @param array  $data
   * 
   * @return null|int
   */
  protected function mail(string $template, array $data)
  {
    $message = $this->container->get('message');
    $view = $this->container->get('view');

    $message->setSubject($data['subject']);
    $message->setFrom($data['from']);
    $message->setTo($data['to']);
    $message->setBody($view->render($template, $data['body']), 'text/html');

    $mailer = $this->container->get('mailer');

    $checkRecipents = $mailer->send($message);

    if($checkRecipents === 0) {
      return $checkRecipents;
    }

    return null;
  }

  /**
   * Validate function
   * 
   * @param array $data
   * @param array $rules
   * 
   * @return null|array
   */
  protected function validate(array $data, array $rules)
  {
    $validator = $this->container->get('validator');

    $validation = $validator->validate($data, $rules);

    $checkValidation = $validation->fails();

    if($checkValidation) {
      return $validation->errors()->all();
    }

    return null;
  }
}
