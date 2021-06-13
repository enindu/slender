<?php

$container->set("parsedown-extra", function(): ParsedownExtra {
  $parsedownExtra = new ParsedownExtra();
  $parsedownExtra->setSafeMode($_ENV["parsedown-extra"]["safe-mode"]);
  return $parsedownExtra;
});
