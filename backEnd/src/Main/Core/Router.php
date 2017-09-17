<?php

namespace Main\Core;

class Router
{
    public $routes;
    public $sitePath;

    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->sitePath = isset($_GET['sitePath']) ? '/'.trim($_GET['sitePath'], ' /') : "/";
    }

    public function getResponse()
    {
        foreach ($this->routes as $routRexExp => $method) {
            $routRexExp = '/'.str_replace('/', '\/', $routRexExp).'/';
            if (preg_match($routRexExp, $this->sitePath)) {
                $parseRoute = explode(":", $this->routes[$this->sitePath]);
                if (is_callable(array($parseRoute[0], $parseRoute[1]))) {
                    $controller = new $parseRoute[0];
                    return $controller->{$parseRoute[1]}();
                }
            }
        }
        return null;
    }
}
