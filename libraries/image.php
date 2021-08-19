<?php

use Intervention\Image\ImageManager;

$image = function(): ImageManager {
    return new ImageManager([
        "driver" => $_ENV["image"]["driver"]
    ]);
};

$container->set("image", $image);
