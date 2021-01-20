<?php

use voku\helper\HtmlMin;

$container->set('html-minify', function(): HtmlMin {
  // Create HTML min
  $htmlMin = new HtmlMin();
  $htmlMin->doOptimizeViaHtmlDomParser();
  $htmlMin->doRemoveComments();
  $htmlMin->doSumUpWhitespace();
  $htmlMin->doRemoveWhitespaceAroundTags();
  $htmlMin->doOptimizeAttributes();
  $htmlMin->doRemoveEmptyAttributes();
  $htmlMin->doRemoveValueFromEmptyInput();
  $htmlMin->doSortCssClassNames();
  $htmlMin->doSortHtmlAttributes();
  $htmlMin->doRemoveSpacesBetweenTags();

  // Return HTML min
  return $htmlMin;
});
