<?php

namespace System\Slender;

class Crypto extends Characters
{
  public static function token(int $length, bool $secure = false): string
  {
    $characters = self::$uppercaseCharacters . self::$lowercaseCharacters . self::$numericCharacters;
    if($secure) {
      $characters .= self::$specialCharacters;
    }

    $characters = Text::shuffle($characters);
    $charactersLength = strlen($characters);

    $token = "";
    for($i = 0; $i < $length; $i++) {
      $randomNumber = random_int(0, $charactersLength - 1);
      $token .= $characters[$randomNumber];
    }

    return $token;
  }

  public static function uniqueID(): string
  {
    $uniqueID = uniqid("", true);
    $uniqueID = str_replace(".", "", $uniqueID);
    $token = self::token(42, true);
    $uniqueID = Text::shuffle($uniqueID . $token);

    return $uniqueID;
  }

  public static function apiKey(): string
  {
    $uniqueID = uniqid("", true);
    $uniqueID = str_replace(".", "", $uniqueID);
    $token = self::token(10);
    $characters = str_split($uniqueID . $token);
    $charactersLength = count($characters);

    $apiKey = "";
    for($i = 0; $i < $charactersLength; $i++) {
      if($i % 8 == 0) {
        $apiKey .= "-";
      }

      $apiKey .= $characters[$i];
    }

    $apiKey = ltrim($apiKey, "-");
    $apiKey = strtoupper($apiKey);

    return $apiKey;
  }
}
