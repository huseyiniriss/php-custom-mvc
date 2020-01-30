<?php

class Router
{
    public static function get(string $route, string $controller, string $callback){
        self::getCallback($route, 'GET', $controller, $callback);
    }

    public static function post(string $route, string $controller, string $callback){
        self::getCallback($route, 'POST', $controller, $callback);
    }

    public static function getCallback($route, $requestType, $controller, $callback){
        if ($_SERVER['PATH_INFO'] === $route && $_SERVER['REQUEST_METHOD'] == $requestType) {
            if (is_callable([__NAMESPACE__.$controller, $callback])){
                call_user_func([__NAMESPACE__.$controller, $callback], $_GET);
            } else {
                die("not exist $callback function");
            }
        }
    }
}
