<?php

namespace App\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Globals extends AbstractExtension implements GlobalsInterface
{
  public function getGlobals(): array
  {
    return [
      'app' => [
        'name'        => $_ENV['APP_NAME'],
        'description' => $_ENV['APP_DESCRIPTION'],
        'keywords'    => $_ENV['APP_KEYWORDS'],
        'author'      => $_ENV['APP_AUTHOR'],
        'url'         => $_ENV['APP_URL']
      ]
    ];
  }
}
