<?php

namespace System\Slender;

class Password
{
  public static function create(string $password, string $salt, string $algorithm = PASSWORD_ARGON2ID): string
  {
    return password_hash($password . $salt . $_ENV["app"]["key"], $algorithm);
  }

  public static function verify(string $password, string $salt, string $hash): bool
  {
    return password_verify($password . $salt . $_ENV["app"]["key"], $hash);
  }
}
