<?php

use Rakit\Validation\Validator;

$validate = function(): Validator {
    return new Validator();
};

$container->set("validate", $validate);
