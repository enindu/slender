<?php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

$mailer = function(): Mailer {
    $transport = Transport::fromDsn($_ENV["mailer"]["protocol"] . "://" . $_ENV["mailer"]["username"] . ":" . $_ENV["mailer"]["password"] . "@" . $_ENV["mailer"]["host"] . ":" . $_ENV["mailer"]["port"]);
    return new Mailer($transport);
};

$container->set("mailer", $mailer);
