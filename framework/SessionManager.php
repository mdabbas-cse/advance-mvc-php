<?php

namespace MVC\Framework;

/**
 * Session manager
 */
class SessionManager
{
  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function get(string $key, $default = null)
  {
    return $_SESSION[$key] ?? $default;
  }

  public function set(string $key, $value): void
  {
    $_SESSION[$key] = $value;
  }

  public function has(string $key): bool
  {
    return isset($_SESSION[$key]);
  }

  public function remove(string $key): void
  {
    unset($_SESSION[$key]);
  }

  public function clear(): void
  {
    session_destroy();
  }

  public function flash(string $key, $value = null)
  {
    if ($value === null) {
      $data = $this->get("flash_$key");
      $this->remove("flash_$key");
      return $data;
    }

    $this->set("flash_$key", $value);
  }
}