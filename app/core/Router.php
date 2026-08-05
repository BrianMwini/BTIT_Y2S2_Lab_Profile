<?php
/**
 * =====================================================================
 * MPVS — Router
 * Maps (method, path) pairs to controller actions. Supports simple
 * path parameters written as {name}, e.g. transactions/show/{id}.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<int,array{0:string,1:string,2:string}> [method, pattern, action] */
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes[] = ['GET', $path, $action];
    }

    public function post(string $path, string $action): void
    {
        $this->routes[] = ['POST', $path, $action];
    }

    /**
     * Dispatch the current request.
     *
     * @param string $method HTTP method
     * @param string $path   Request path WITHOUT leading slash ('' = dashboard)
     */
    public function dispatch(string $method, string $path): void
    {
        $path = trim($path, '/');

        foreach ($this->routes as [$routeMethod, $pattern, $action]) {
            if ($routeMethod !== $method) {
                continue;
            }

            // Convert {param} segments to named regex groups.
            $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('#^' . $regex . '$#', $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($action, $params);
                return;
            }
        }

        // No route matched.
        http_response_code(404);
        require VIEWS_PATH . '/errors/404.php';
    }

    /** Instantiate the controller and call the method with route params. */
    private function call(string $action, array $params): void
    {
        [$controllerName, $method] = explode('@', $action);
        $class = 'App\\Controllers\\' . $controllerName;
        $controller = new $class();
        $controller->{$method}($params);
    }
}
