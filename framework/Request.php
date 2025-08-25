<?php

namespace MVC\Framework;


/**
 * HTTP Request handler
 */
class Request
{
  private string $method;
  private string $uri;
  private array $params;
  private array $body;

  public function __construct()
  {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $this->params = $_GET;

    // Parse request body
    $input = file_get_contents('php://input');
    $this->body = json_decode($input, true) ?? $_POST;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getUri(): string
  {
    return $this->uri;
  }

  public function getParams(): array
  {
    return $this->params;
  }

  public function getBody(): array
  {
    return $this->body;
  }

  public function get(string $key, $default = null)
  {
    return $this->params[$key] ?? $default;
  }

  public function post(string $key, $default = null)
  {
    return $this->body[$key] ?? $default;
  }
}