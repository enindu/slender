<?php

use Rakit\Validation\Validator;

$validation = function(): Validator {
    return new Validator();
};

$container->set("validation", $validation);
