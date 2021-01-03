<?php

$container->set('markdown', function(): Parsedown {
  // Create parsedown
  $parsedown =  new Parsedown();
  $parsedown->setSafeMode(true);

  // Return parsedown
  return $parsedown;
});
