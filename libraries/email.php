<?php

use Symfony\Component\Mime\Email;

$library = function(): Email {
    return new Email();
};

$container->set("email", $library);
