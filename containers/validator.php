<?php

use Rakit\Validation\Validator;

$container->set('validator', function(): Validator {
  return new Validator();
});
