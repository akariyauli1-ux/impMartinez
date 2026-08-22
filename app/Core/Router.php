<?php
class Router {
    protected $routes = [];
    
    public function add($method, $uri, $controller, $action) {
        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function dispatch($uri, $method) {
        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            $controllerFile = __DIR__ . '/../Controllers/' . $route['controller'] . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $route['controller']();
                $action = $route['action'];
                return $controller->$action();
            }
        }
        
        http_response_code(404);
        echo "404 - Página no encontrada";
        exit;
    }
}
