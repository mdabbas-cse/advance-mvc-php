<?php

namespace MVC\Framework;

/**
 * Simple view engine
 */
class ViewEngine
{
  private string $viewsDir;
  private string $cacheDir;
  private array $globals = [];

  public function __construct(string $viewsDir, string $cacheDir)
  {
    $this->viewsDir = rtrim($viewsDir, '/') . '/';
    $this->cacheDir = rtrim($cacheDir, '/') . '/';

    if (!is_dir($this->cacheDir)) {
      mkdir($this->cacheDir, 0755, true);
    }
  }

  /**
   * Render template
   */
  public function render(string $template, array $data = []): string
  {
    $templateFile = $this->viewsDir . $template . '.php';

    if (!file_exists($templateFile)) {
      throw new \RuntimeException("Template '$template' not found");
    }

    $allData = array_merge($this->globals, $data);

    extract($allData, EXTR_SKIP);

    ob_start();
    include $templateFile;
    return ob_get_clean();
  }

  /**
   * Add global variable
   */
  public function addGlobal(string $name, $value): void
  {
    $this->globals[$name] = $value;
  }

  /**
   * Escape HTML
   */
  public function escape($value): string
  {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
  }
}