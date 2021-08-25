<?php

namespace System\Slim;

use DI\Container;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface
{
    public function __construct(private Container $container)
    {
        $this->container->get("eloquent");
    }

    public function __invoke(Throwable $throwable, bool $displayErrorDetails): string
    {
        $type = "Internal/Server";
        if($throwable instanceof HttpException) {
            $type = "HTTP";
        }

        $twig = $this->container->get("twig");
        return $twig->render("@common/error.twig", [
            "type"    => $type,
            "code"    => $throwable->getCode(),
            "message" => $throwable->getMessage(),
            "file"    => $throwable->getFile(),
            "line"    => $throwable->getLine(),
            "traces"  => $throwable->getTrace()
        ]);
    }
}
