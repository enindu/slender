<?php

$parsedownExtra = function(): ParsedownExtra {
    $parsedownExtra = new ParsedownExtra();

    $parsedownExtra->setSafeMode($_ENV["parsedown-extra"]["safe-mode"]);
    return $parsedownExtra;
};

$container->set("parsedown-extra", $parsedownExtra);
