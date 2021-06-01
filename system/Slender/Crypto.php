<?php

namespace System\Slender;

class Crypto
{
  private static $upper = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
  private static $lower = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
  private static $numeric = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

  public static function token(int $length = 32): string
  {
    $characters = array_merge(self::$upper, self::$lower, self::$numeric);
    shuffle($characters);

    $token = "";
    for($i = 0; $i < $length; $i++) {
      $charactersLength = count($characters);
      $randomNumber = random_int(0, $charactersLength - 1);
      $token .= $characters[$randomNumber];
    }

    return $token;
  }

  public static function uniqueID(): string
  {
    return str_replace(".", "-", Text::randomCase(uniqid(self::token(6) . "-", true)));
  }
}
