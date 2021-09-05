<?php

namespace System\View;

use DI\Container;
use Twig\Extension\AbstractExtension;

class Filters extends AbstractExtension
{
    public function __construct(private Container $container) {}

    public function getFilters(): array
    {
        return [];
    }
}
