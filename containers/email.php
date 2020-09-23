<?php

$container->set('mailer', function(): Swift_Mailer {
  $smtpTransport = new Swift_SmtpTransport();

  $smtpTransport->setHost($_ENV['EMAIL_HOST']);
  $smtpTransport->setPort($_ENV['EMAIL_PORT']);
  $smtpTransport->setUsername($_ENV['EMAIL_USERNAME']);
  $smtpTransport->setPassword($_ENV['EMAIL_PASSWORD']);

  return new Swift_Mailer($smtpTransport);
});

$container->set('message', function(): Swift_Message {
  return new Swift_Message();
});
