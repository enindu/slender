<?php

namespace App\Controllers;

use DI\Container;
use Slim\Psr7\Response;

class Controller
{
  protected $container;

  /**
   * Base constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
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
   * Email function
   * 
   * @param string $template
   * @param array  $data
   * 
   * @return null|integer
   */
  protected function email(string $template, array $data)
  {
    $message = $this->container->get('message');
    $view    = $this->container->get('view');

    $message->setSubject($data['subject']);
    $message->setFrom($data['from']);
    $message->setTo($data['to']);
    $message->setBody($view->render($template, $data['body']), 'text/html');

    $mailer = $this->container->get('mailer');

    $mailRecipients = $mailer->send($message);
    if($mailRecipients == 0) {
      return $mailRecipients;
    }

    return;
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

    $validate = $validator->validate($data, $rules);

    $validationFails = $validate->fails();
    if($validationFails) {
      return $validate->errors()->all();
    }
    
    return;
  }
}
