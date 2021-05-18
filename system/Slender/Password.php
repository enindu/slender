<?php

namespace System\Slender;

class Password
{
  public static function create(string $password, string $algorithm = PASSWORD_ARGON2ID): string|null
  {
    $hash = password_hash($password . $_ENV["app"]["key"], $algorithm);
    if($hash == false) {
      return null;
    }

    return $hash;
  }

  public static function verify(string $password, string $hash): bool
  {
    return password_verify($password . $_ENV["app"]["key"], $hash);
  }
}
