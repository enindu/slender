<?php

use Symfony\Component\Filesystem\Filesystem;

$library = function(): Filesystem {
    return new Filesystem();
};

$container->set("filesystem", $library);
