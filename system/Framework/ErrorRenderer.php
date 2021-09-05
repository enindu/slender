<?php

namespace System\Framework;

use DI\Container;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface
{
    public function __construct(private Container $container)
    {
        $this->container->get("database");
    }

    public function __invoke(Throwable $throwable, bool $displayErrorDetails): string
    {
        $view = $this->container->get("view");
        return $view->render("template/error.twig", [
            "type"    => $throwable instanceof HttpException ? "http" : "internal",
            "code"    => $throwable->getCode() ?? 500,
            "message" => $throwable->getMessage() ?? "Internal server error.",
            "file"    => $throwable->getFile() ?? "N/A",
            "line"    => $throwable->getLine() ?? "N/A",
            "traces"  => $throwable->getTrace() ?? ["No trace found."]
        ]);
    }
}
