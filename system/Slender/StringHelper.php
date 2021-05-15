<?php

namespace System\Slender;

class StringHelper
{
  public static function createSlug(string $text): string
  {
    $characters = ["`", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "'", "\"", ",", "<", ".", ">", "/", "?"];
    $text = str_replace($characters, "", trim($text));

    $sanitizedWords = [];
    $words = explode(" ", $text);
    foreach($words as $item) {
      if($item != "") {
        array_push($sanitizedWords, $item);
      }
    }

    return strtolower(uniqid(implode("-", $sanitizedWords) . "-"));
  }
}
