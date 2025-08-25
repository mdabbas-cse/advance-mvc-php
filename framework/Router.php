<?php

namespace MVC\Framework;

/**
 * URL Router
 */
class Router
{
  private array $routes = [];

  /**
   * Add route for different HTTP methods
   */
  public function get(string $pattern, string $handler): void
  {
    $this->addRoute('GET', $pattern, $handler);
  }

  public function post(string $pattern, string $handler): void
  {
    $this->addRoute('POST', $pattern, $handler);
  }

  public function put(string $pattern, string $handler): void
  {
    $this->addRoute('PUT', $pattern, $handler);
  }

  public function delete(string $pattern, string $handler): void
  {
    $this->addRoute('DELETE', $pattern, $handler);
  }

  /**
   * Add route to collection
   */
  private function addRoute(string $method, string $pattern, string $handler): void
  {
    $this->routes[] = [
      'method' => $method,
      'pattern' => $pattern,
      'handler' => $handler,
      'regex' => $this->convertPatternToRegex($pattern),
      'params' => $this->extractParamNames($pattern)
    ];
  }

  /**
   * Convert URL pattern to regex
   */
  private function convertPatternToRegex(string $pattern): string
  {
    $pattern = preg_quote($pattern, '/');
    $pattern = preg_replace('/\\\{([^}]+)\\\}/', '(?P<$1>[^/]+)', $pattern);
    return '/^' . $pattern . '$/';
  }

  /**
   * Extract parameter names from pattern
   */
  private function extractParamNames(string $pattern): array
  {
    preg_match_all('/\{([^}]+)\}/', $pattern, $matches);
    return $matches[1];
  }

  /**
   * Dispatch request to controller
   */
  public function dispatch(Request $request, Container $container): Response
  {
    $method = $request->getMethod();
    $uri = $request->getUri();

    foreach ($this->routes as $route) {
      if ($route['method'] === $method && preg_match($route['regex'], $uri, $matches)) {
        // Extract route parameters
        $params = [];
        foreach ($route['params'] as $param) {
          $params[$param] = $matches[$param] ?? null;
        }

        // Parse controller and action
        [$controllerName, $action] = explode('@', $route['handler']);

        // Create controller instance
        $controller = $this->createController($controllerName, $container);

        // Call controller action
        return $controller->$action($request, $params);
      }
    }

    throw new \RuntimeException('Route not found', 404);
  }

  /**
   * Create controller instance
   */
  private function createController(string $controllerName, Container $container): BaseController
  {
    if (!class_exists($controllerName)) {
      throw new \RuntimeException("Controller '$controllerName' not found");
    }

    return new $controllerName($container);
  }
}