<?php

namespace MVC\Framework;

class Container
{
  private array $services = [];
  private array $instances = [];

  /**
   * Register a service
   */
  public function register(string $name, callable $factory): void
  {
    $this->services[$name] = $factory;
  }

  /**
   * Get service instance
   */
  public function get(string $name)
  {
    if (!isset($this->instances[$name])) {
      if (!isset($this->services[$name])) {
        throw new \InvalidArgumentException("Service '$name' not found");
      }

      $this->instances[$name] = call_user_func($this->services[$name]);
    }

    return $this->instances[$name];
  }

  /**
   * Check if service exists
   */
  public function has(string $name): bool
  {
    return isset($this->services[$name]);
  }
}