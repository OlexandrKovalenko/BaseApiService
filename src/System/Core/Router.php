<?php

namespace App\System\Core;

use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Container\Container;
use Exception;

/**
 * Class Router
 *
 * @package App\System\Core
 * @author maslo
 * @since 08.11.2024
 */
class Router
{
    /**
     * @var array $routes
     */
    private array $routes = [];
    /**
     * @var Container $container
     */
    private Container $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * addRoute
     *
     * @param string $method
     * @param string $uri
     * @param $action
     * @return void
     */
    public function addRoute(string $method, string $uri, $action): void
    {
        $uri = trim($uri, '/');
        $this->routes[strtoupper($method)][] = [
            'pattern' => $this->convertUriToPattern($uri),
            'action' => $action,
            'parameters' => $this->extractUriParameters($uri)
        ];
    }

    /**
     * get
     *
     * @param string $uri
     * @param $action
     * @return void
     */
    public function get(string $uri, $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * post
     *
     * @param string $uri
     * @param $action
     * @return void
     */
    public function post(string $uri, $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * put
     *
     * @param string $uri
     * @param $action
     * @return void
     */
    public function put(string $uri, $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * delete
     *
     * @param string $uri
     * @param $action
     * @return void
     */
    public function delete(string $uri, $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * group
     *
     * @param string $prefix
     * @param callable $callback
     * @return void
     */
    public function group(string $prefix, callable $callback): void
    {
        $originalRoutes = $this->routes;
        $this->routes = [];

        call_user_func($callback, $this);

        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route) {
                $this->addRoute($method, $prefix . '/' . ltrim($route['pattern'], '/'), $route['action']);
            }
        }

        $this->routes = array_merge($originalRoutes, $this->routes);
    }

    /**
     * convertUriToPattern
     *
     * @param string $uri
     * @return string
     */
    protected function convertUriToPattern(string $uri): string
    {
        $uri = str_replace(['^', '$'], '', $uri);
        $uri = trim($uri, '/');

        if ($uri === '') {
            return '/^$/';
        }

        $pattern = preg_replace('/\{[^}]+\}/', '([^\/]+)', $uri);

        $pattern = str_replace('/', '\/', $pattern);

        return '/^' . $pattern . '$/';
    }


    /**
     * extractUriParameters
     *
     * @param string $uri
     * @return array
     */
    private function extractUriParameters(string $uri): array
    {
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);
        return $matches[1];
    }

    /**
     * dispatch
     *
     * @param string $method
     * @param string $uri
     * @return void
     * @throws Exception
     */
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = trim($uri, '/');

        if (!isset($this->routes[$method])) {
            $this->respondNotFound();
            return;
        }
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                //$parameters = array_combine($route['parameters'], $matches);
                //$headers = getallheaders();
                $request = new RequestBundle($uri);
                $this->handleAction($route['action'], $request);
                return;
            }
        }

        $this->respondNotFound();
    }

    /**
     * handleAction
     *
     * @param $action
     * @param RequestBundle $request
     * @return void
     * @throws Exception
     */
    private function handleAction($action, RequestBundle $request): void
    {
        if (is_callable($action)) {
            // Якщо це callable, одразу викликаємо його з request
            $response = call_user_func($action, $request);
        } elseif (is_array($action) && isset($action[0]) && is_array(end($action))) {

            // Якщо $action містить мідлвари та контролер
            $this->handleMiddleware($action, $request);
            return;
        } elseif (is_array($action) && isset($action[0]) && is_string($action[0])) {
            // Якщо це масив і перший елемент — клас контролера
            list($controller, $method) = $action;
            $response = $this->callControllerAction($controller, $method, $request);
        } else {
            http_response_code(500);
            echo 'Invalid route action';
            return;
        }

        // Відправляємо відповідь, якщо контролер був викликаний напряму
        $response?->send();
    }

    /**
     * handleMiddleware
     *
     * @param array $action
     * @param RequestBundle $request
     * @return void
     * @throws Exception
     */
    private function handleMiddleware(array $action, RequestBundle $request): void
    {
        $middlewares = array_slice($action, 0, -1); // Мідлвари — всі елементи, крім останнього
        $controllerAction = end($action); // Останній елемент — контролер та метод

        // Замикання для виклику контролера після мідлварів
        $next = function ($request, $response) use ($controllerAction) {
            list($controller, $method) = $controllerAction;
            return $this->callControllerAction($controller, $method, $request, $response);
        };

        // Запускаємо кожен мідлвар у зворотному порядку
        foreach (array_reverse($middlewares) as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                http_response_code(500);
                echo "Middleware {$middlewareClass} not found";
                return;
            }

            $middlewareInstance = $this->container->make($middlewareClass);

            $next = function ($request, $response) use ($middlewareInstance, $next) {
                return $middlewareInstance->handle($request, $response, $next);
            };
        }

        $response = $next($request, new ResponseBundle());

        foreach ($middlewares as $middlewareClass) {
            $middlewareInstance = $this->container->make($middlewareClass);
            if (method_exists($middlewareInstance, 'after')) {
                $response = $middlewareInstance->after($response);
            }
        }

        $response->send();
    }


    /**
     * callControllerAction
     *
     * @param string $controller
     * @param string $method
     * @param RequestBundle $request
     * @return ResponseBundle
     * @throws Exception
     */
    private function callControllerAction(string $controller, string $method, RequestBundle $request): ResponseBundle
    {
        // Оскільки ми додаємо зв'язок для контролерів у контейнер, тут можемо зробити так:
        $controllerInstance = $this->container->make($controller);

        if ($controllerInstance) {
            if (method_exists($controllerInstance, $method)) {
                // Викликаємо метод контролера
                return call_user_func([$controllerInstance, $method], $request);
            } else {
                http_response_code(500);
                echo "Method {$method} not found in controller {$controller}";
            }
        } else {
            http_response_code(500);
            echo "Controller {$controller} not found";
        }

        return new ResponseBundle(500, ['error' => 'Internal Server Error']);
    }

    /**
     * respondNotFound
     *
     * @return void
     */
    private function respondNotFound(): void
    {
        http_response_code(404);
        (new ResponseBundle(404, ['error' => '404 Not Found']))->send();
    }
}
