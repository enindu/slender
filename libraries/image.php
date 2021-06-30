<?php

use Intervention\Image\ImageManager;

$library = function(): ImageManager {
    return new ImageManager([
        "driver" => $_ENV["image"]["driver"]
    ]);
};

$container->set("image", $library);
