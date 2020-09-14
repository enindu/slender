<?php

use Intervention\Image\ImageManager;

$container->set('image', function(): ImageManager {
  return new ImageManager([
    'driver' => $_ENV['IMAGE_DRIVER']
  ]);
});
