<?php

namespace MVC\Framework;

use \PDO;

/**
 * Application entry point and front controller
 */
class Application
{
  private Router $router;
  private array $config;
  private Container $container;

  public function __construct(array $config = [])
  {
    $this->config = $config;
    $this->container = new Container();
    $this->router = new Router();

    $this->registerServices();
    $this->setupRoutes();
  }

  /**
   * Register application services
   */
  private function registerServices(): void
  {
    // Database connection
    $this->container->register('database', function () {
      $dsn = "mysql:host={$this->config['db_host']};dbname={$this->config['db_name']}";
      return new PDO($dsn, $this->config['db_user'], $this->config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    });

    // Template engine
    $this->container->register('view', function () {
      return new ViewEngine('views/', 'cache/');
    });

    // Session manager
    $this->container->register('session', function () {
      return new SessionManager();
    });
  }

  /**
   * Setup application routes
   */
  private function setupRoutes(): void
  {
    // Home routes
    $this->router->get('/', 'HomeController@index');
    $this->router->get('/about', 'HomeController@about');

    // User routes
    $this->router->get('/users', 'UserController@index');
    $this->router->get('/users/{id}', 'UserController@show');
    $this->router->post('/users', 'UserController@store');
    $this->router->put('/users/{id}', 'UserController@update');
    $this->router->delete('/users/{id}', 'UserController@destroy');

    // Authentication routes
    $this->router->get('/login', 'AuthController@showLogin');
    $this->router->post('/login', 'AuthController@login');
    $this->router->post('/logout', 'AuthController@logout');
  }

  /**
   * Run the application
   */
  public function run(): void
  {
    try {
      $request = new Request();
      $response = $this->router->dispatch($request, $this->container);
      $response->send();
    } catch (\Exception $e) {
      $this->handleError($e);
    }
  }

  /**
   * Handle application errors
   */
  private function handleError(\Exception $e): void
  {
    $errorController = new ErrorController($this->container);
    $response = $errorController->handle($e);
    $response->send();
  }
}