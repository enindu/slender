<?php

use Symfony\Component\Filesystem\Filesystem;

$filesystem = function(): Filesystem {
    return new Filesystem();
};

$container->set("filesystem", $filesystem);
