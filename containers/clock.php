<?php

use Carbon\Carbon;

$container->set('clock', function(): Carbon {
  return new Carbon();
});
