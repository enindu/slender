<?php

namespace System\Slender;

class Text extends Characters
{
  public static function clean(string $text): string
  {
    $characters = str_split(self::$specialCharacters);
    $text = trim($text);
    $cleanText = str_replace($characters, "", $text);

    return $cleanText;
  }

  public static function shuffle(string $text): string
  {
    $text = trim($text);
    $characters = str_split($text);
    shuffle($characters);

    $shuffleText = implode($characters);
    return $shuffleText;
  }

  public static function sentencecase(string $text): string
  {
    $text = trim($text);
    $text = strtolower($text);
    $sentences = explode(". ", $text);

    $sentencecaseText = "";
    foreach($sentences as $sentence) {
      if($sentence == "") {
        continue;
      }

      $sentence = trim($sentence);
      $sentence = rtrim($sentence, ".");
      $sentence = ucfirst($sentence);
      $sentencecaseText .= $sentence . ". ";
    }

    $sentencecaseText = trim($sentencecaseText);
    return $sentencecaseText;
  }

  public static function slug(string $text): string
  {
    $text = trim($text);
    $text = self::clean($text);
    $words = explode(" ", $text);

    $slugText = "";
    foreach($words as $word) {
      if($word == "") {
        continue;
      }

      $slugText .= $word . "-";
    }

    $slugText = strtolower($slugText);
    $token = Crypto::token(8);
    $slugText = $slugText . $token;

    return $slugText;
  }

  public static function validationMessage(array $messages): string
  {
    $message = reset($messages);
    $message = trim($message);
    $message = self::sentencecase($message);
    $message = str_replace("-", " ", $message);

    return $message;
  }
}
