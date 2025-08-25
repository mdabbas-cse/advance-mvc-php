<?php

namespace MVC\Framework;

/**
 * Error controller
 */
class ErrorController extends BaseController
{
  /**
   * Handle application errors
   */
  public function handle(\Exception $e): Response
  {
    $statusCode = $e->getCode() ?: 500;
    $message = $e->getMessage();

    $data = [
      'title' => 'Error',
      'status_code' => $statusCode,
      'message' => $message,
      'user' => $this->getUser()
    ];

    $content = $this->view->render('errors/error', $data);
    return new Response($content, $statusCode);
  }
}