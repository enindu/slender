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
    // Get container
    $this->container = $container;

    // Get libraries
    $this->filesystem = $container->get('filesystem');
    $this->clock = $container->get('clock');
    $this->image = $container->get('image');

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
    $response->withHeader('content-type', 'text/html')->getBody()->write($view->render($template, $data));

    return $response;
  }

  /**
   * Email function
   * 
   * @param string $template
   * @param array  $data
   * 
   * @return null|int
   */
  protected function email(string $template, array $data)
  {
    // Get message and view libraries
    $message = $this->container->get('message');
    $view = $this->container->get('view');

    // Configure email
    $message->setSubject($data['subject']);
    $message->setFrom($data['from']);
    $message->setTo($data['to']);
    $message->setBody($view->render($template, $data['body']), 'text/html');

    // Get mailer library
    $mailer = $this->container->get('mailer');

    // Check recipients
    $checkRecipients = $mailer->send($message);

    if($checkRecipients === 0) {
      return $checkRecipients;
    }

    // Return null
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
    // Get validator library
    $validator = $this->container->get('validator');

    // Check validation
    $validation = $validator->validate($data, $rules);
    $checkValidation = $validation->fails();

    if($checkValidation) {
      return $checkValidation->errors()->all();
    }

    // Return null
    return null;
  }
}
