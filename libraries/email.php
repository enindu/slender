<?php

use Symfony\Component\Mime\Email;

$email = function(): Email {
    return new Email();
};

$container->set("email", $email);
