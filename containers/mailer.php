<?php

$container->set('mailer', function(): Swift_Mailer {
  $smtpTransport = new Swift_SmtpTransport();

  $smtpTransport->setHost($_ENV['mailer']['host']);
  $smtpTransport->setPort($_ENV['mailer']['port']);
  $smtpTransport->setUsername($_ENV['mailer']['username']);
  $smtpTransport->setPassword($_ENV['mailer']['password']);

  return new Swift_Mailer($smtpTransport);
});