<?php

namespace App\Utilities;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class TwigGlobals extends AbstractExtension implements GlobalsInterface
{
  public function getGlobals(): array
  {
    return [];
  }
}
