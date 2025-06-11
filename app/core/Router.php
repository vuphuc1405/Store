<?php
class Router {
    private $routes = [];

    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = str_replace('/mystore', '', $requestUri);
        
        if (empty($requestUri) || $requestUri === '/') {
            $requestUri = '/home';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestUri)) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                
                require_once "app/controllers/{$controllerName}.php";
                $controller = new $controllerName();
                
                if (method_exists($controller, $actionName)) {
                    $controller->$actionName();
                    return;
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "Page not found";
    }

    private function matchPath($routePath, $requestUri) {
        // Simple pattern matching - có thể mở rộng cho dynamic routes
        return $routePath === $requestUri;
    }
}