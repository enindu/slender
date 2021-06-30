<?php

namespace System\Twig;

use DI\Container;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Filters extends AbstractExtension
{
    public function __construct(private Container $container) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter("content", [$this, "content"]),
            new TwigFilter("limit", [$this, "limit"]),
            new TwigFilter("markdown", [$this, "markdown"])
        ];
    }

    public function content(string $file): string
    {
        $content = file_get_contents($file);
        if(!$content) {
            $error = "Cannot get content from " . $file . " file";
            throw new RuntimeError($error);
        }

        return $content;
    }

    public function limit(string $text, int $length = 100): string
    {
        $textLength = strlen($text);
        if($textLength < $length) {
            return $text;
        }

        $text = substr($text, 0, $length);
        return $text . "...";
    }

    public function markdown(string $text): string
    {
        $parsedownExtra = $this->container->get("parsedown-extra");
        return $parsedownExtra->text($text);
    }
}
