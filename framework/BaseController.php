<?php

namespace MVC\Framework;

use \PDO;

/**
 * Base controller class
 */
abstract class BaseController
{
  protected Container $container;
  protected ViewEngine $view;
  protected PDO $database;
  protected SessionManager $session;

  public function __construct(Container $container)
  {
    $this->container = $container;
    $this->view = $container->get('view');
    $this->database = $container->get('database');
    $this->session = $container->get('session');
  }

  /**
   * Render view with data
   */
  protected function render(string $template, array $data = []): Response
  {
    $content = $this->view->render($template, $data);
    return new Response($content);
  }

  /**
   * Return JSON response
   */
  protected function json(array $data, int $statusCode = 200): Response
  {
    return (new Response())->json($data)->setStatusCode($statusCode);
  }

  /**
   * Redirect to URL
   */
  protected function redirect(string $url): Response
  {
    return (new Response())->redirect($url);
  }

  /**
   * Get authenticated user
   */
  protected function getUser(): ?array
  {
    return $this->session->get('user');
  }

  /**
   * Check if user is authenticated
   */
  protected function isAuthenticated(): bool
  {
    return $this->getUser() !== null;
  }
}