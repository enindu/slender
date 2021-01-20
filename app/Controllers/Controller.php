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
    // Get dependency container
    $this->container = $container;

    // Run database library
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
    // Get view library
    $view = $this->container->get('view');

    // Return response
    $response->getBody()->write($view->render($template, $data));
    return $response->withHeader('content-type', 'text/html');
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
    // Get message and view libraries
    $message = $this->container->get('message');
    $view = $this->container->get('view');

    // Create message
    $message->setSubject($data['subject']);
    $message->setFrom($data['from']);
    $message->setTo($data['to']);
    $message->setBody($view->render($template, $data['body']), 'text/html');

    // Get mailer library
    $mailer = $this->container->get('mailer');

    // Check email recipients
    $emailRecipients = $mailer->send($message);
    if($emailRecipients == 0) {
      return $emailRecipients;
    }

    // Return null
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
    // Get validator library
    $validator = $this->container->get('validator');

    // Check validation fails
    $validate = $validator->validate($data, $rules);
    $validationFails = $validate->fails();
    if($validationFails) {
      return $validate->errors()->all();
    }
    
    // Return null
    return;
  }

  /**
   * Auth function
   * 
   * @param string $key
   * @param string $type
   * 
   * @return string|null
   */
  protected function auth(string $key, string $type)
  {
    // Check session exists
    $sessionExists = isset($_SESSION['auth'][$type][$key]);
    if(!$sessionExists) {
      return;
    }

    // Return session value
    return $_SESSION['auth'][$type][$key];
  }
}
