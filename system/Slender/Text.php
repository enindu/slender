<?php

namespace System\Slender;

class Text
{
  private static $special = ["`", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "_", "=", "+", "[", "]", "{", "}", "|", ";", ":", "'", "\"", ",", "<", ".", ">", "\\", "/", "?"];

  public static function clean(string $text): string
  {
    return str_replace(self::$special, "", trim($text));
  }

  public static function randomCase(string $text): string
  {
    $randomCase = "";
    $characters = str_split(trim($text));
    foreach($characters as $item) {
      $randomNumber = random_int(0, PHP_INT_MAX);
      if($randomNumber % 2 == 0) {
        $randomCase .= strtoupper($item);
        continue;
      }

      $randomCase .= $item;
    }

    return $randomCase;
  }

  public static function sentenceCase(string $text): string
  {
    $sentenceCase = "";
    $sentences = explode(".", strtolower(trim($text)));
    foreach($sentences as $item) {
      if($item != "") {
        $sentenceCase .= ucfirst(trim($item)) . ". ";
      }
    }

    return trim($sentenceCase);
  }

  public static function slug(string $text): string
  {
    $slug = "";
    $words = explode(" ", self::clean($text));
    foreach($words as $item) {
      if($item != "") {
        $slug .= $item . "-";
      }
    }

    return $slug . Crypto::uniqueID(16);
  }

  public static function validationMessage(array $messages): string
  {
    return str_replace("-", " ", self::sentenceCase(reset($messages)));
  }
}
