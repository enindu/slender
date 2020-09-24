<?php

$container->set('mailer', function(): Swift_Mailer {
  // Get SMTP transport
  $smtpTransport = new Swift_SmtpTransport();

  // Configure SMTP transport
  $smtpTransport->setHost($_ENV['EMAIL_HOST']);
  $smtpTransport->setPort($_ENV['EMAIL_PORT']);
  $smtpTransport->setUsername($_ENV['EMAIL_USERNAME']);
  $smtpTransport->setPassword($_ENV['EMAIL_PASSWORD']);

  // Return mailer
  return new Swift_Mailer($smtpTransport);
});

$container->set('message', function(): Swift_Message {
  return new Swift_Message();
});
