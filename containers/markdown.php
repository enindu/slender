<?php

$container->set('markdown', function(): Parsedown {
  // Create parsedown
  $parsedown =  new Parsedown();
  $parsedown->setSafeMode($_ENV['markdown']['safe-mode']);

  // Return parsedown
  return $parsedown;
});
