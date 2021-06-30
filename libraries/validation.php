<?php

use Rakit\Validation\Validator;

$library = function(): Validator {
    return new Validator();
};

$container->set("validation", $library);
