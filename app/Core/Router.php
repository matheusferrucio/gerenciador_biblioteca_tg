<?php

class Router
{
    private array $routes = [];

    /**
     * Register a GET route
     */
    public function get(string $path, string $controller, string $method): void
    {
        $this->addRoute('GET', $path, $controller, $method);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, string $controller, string $method): void
    {
        $this->addRoute('POST', $path, $controller, $method);
    }

    /**
     * Add a route to the collection
     */
    private function addRoute(string $httpMethod, string $path, string $controller, string $method): void
    {
        // Convert route parameters like {id} to regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[0-9]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'httpMethod' => $httpMethod,
            'pattern'    => $pattern,
            'controller' => $controller,
            'method'     => $method,
        ];
    }

    /**
     * Dispatch the request to the matching route
     */
    public function dispatch(string $url, string $httpMethod): void
    {
        $url = $this->sanitizeUrl($url);

        foreach ($this->routes as $route) {
            if ($route['httpMethod'] !== $httpMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $url, $matches)) {
                $controllerName = $route['controller'];
                $methodName     = $route['method'];

                // Extract only named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Require and instantiate controller
                $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';

                if (!file_exists($controllerFile)) {
                    $this->error404("Controller '{$controllerName}' not found.");
                    return;
                }

                require_once $controllerFile;

                if (!class_exists($controllerName)) {
                    $this->error404("Class '{$controllerName}' not found.");
                    return;
                }

                $controllerInstance = new $controllerName();

                if (!method_exists($controllerInstance, $methodName)) {
                    $this->error404("Method '{$methodName}' not found in '{$controllerName}'.");
                    return;
                }

                // Call the method with extracted params
                call_user_func_array([$controllerInstance, $methodName], array_values($params));
                return;
            }
        }

        // No route matched
        $this->error404();
    }

    /**
     * Clean and prepare the URL
     */
    private function sanitizeUrl(string $url): string
    {
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url;
    }

    /**
     * Display 404 page
     */
    private function error404(string $message = 'Page not found.'): void
    {
        http_response_code(404);
        echo '<div style="text-align:center; margin-top:100px; font-family:sans-serif;">';
        echo '<h1>404</h1>';
        echo '<p>' . htmlspecialchars($message) . '</p>';
        echo '<a href="' . BASE_URL . '/login">Go to Login</a>';
        echo '</div>';
    }
}
