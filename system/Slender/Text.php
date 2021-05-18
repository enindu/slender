<?php

namespace System\Slender;

class Text
{
  private static $special = ["`", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "_", "=", "+", "[", "]", "{", "}", "|", ";", ":", "'", "\"", ",", "<", ".", ">", "\\", "/", "?"];

  public static function clean(string $text): string
  {
    $text = trim($text);
    return str_replace(self::$special, "", $text);
  }

  public static function randomCase(string $text): string
  {
    $randomCase = "";
    $text = trim($text);
    $characters = str_split($text);
    foreach($characters as $item) {
      $randomNumber = random_int(0, PHP_INT_MAX);
      if($randomNumber % 2 == 0) {
        $randomCase .= strtoupper($$item);
        continue;
      }

      $randomCase .= $item;
    }

    return $randomCase;
  }

  public static function sentenceCase(string $text): string
  {
    $sentenceCase = "";
    $text = trim($text);
    $text = strtolower($text);
    $sentences = explode(".", $text);
    foreach($sentences as $item) {
      if($item != "") {
        $item = trim($item);
        $sentenceCase .= ucfirst($item) . ". ";
      }
    }

    return trim($sentenceCase);
  }

  public static function slug(string $text): string
  {
    $slug = "";
    $text = self::clean($text);
    $words = explode(" ", $text);
    foreach($words as $item) {
      if($item != "") {
        $slug .= $item . "-";
      }
    }

    return $slug . Crypto::uniqueID(16);
  }

  public static function validationMessage(array $messages): string
  {
    $message = reset($messages);
    $message = self::sentenceCase($message);
    return str_replace("-", " ", $message);
  }
}
